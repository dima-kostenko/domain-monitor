<?php

namespace App\Http\Controllers;

use App\Models\Domain;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DomainCheckController extends Controller
{
    public function index(Request $request, Domain $domain): View
    {
        // Ownership guard
        if ($request->user()->id !== $domain->user_id && ! $request->user()->isAdmin()) {
            abort(403);
        }

        $filters = $request->validate([
            'status'   => ['nullable', 'in:online,offline'],
            'date_from'=> ['nullable', 'date', 'before_or_equal:today'],
            'date_to'  => ['nullable', 'date', 'after_or_equal:date_from', 'before_or_equal:today'],
            'per_page' => ['nullable', 'integer', 'in:25,50,100'],
        ]);

        $query = $domain->checks()->latest();

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        $perPage = (int) ($filters['per_page'] ?? 25);
        $checks  = $query->paginate($perPage)->withQueryString();

        // Stats scoped to the current filters (no pagination applied)
        $filteredQuery = $domain->checks();
        if (! empty($filters['status'])) {
            $filteredQuery->where('status', $filters['status']);
        }
        if (! empty($filters['date_from'])) {
            $filteredQuery->whereDate('created_at', '>=', $filters['date_from']);
        }
        if (! empty($filters['date_to'])) {
            $filteredQuery->whereDate('created_at', '<=', $filters['date_to']);
        }

        $filteredTotal  = (clone $filteredQuery)->count();
        $filteredOnline = (clone $filteredQuery)->where('status', 'online')->count();
        $filteredAvg    = (clone $filteredQuery)->where('status', 'online')->avg('response_time');

        $stats = [
            'total'   => $filteredTotal,
            'online'  => $filteredOnline,
            'offline' => $filteredTotal - $filteredOnline,
            'uptime'  => $filteredTotal > 0 ? round($filteredOnline / $filteredTotal * 100, 1) : null,
            'avg_time'=> $filteredAvg !== null ? round($filteredAvg) : null,
        ];

        return view('domain_checks.index', compact('domain', 'checks', 'stats', 'filters'));
    }
}
