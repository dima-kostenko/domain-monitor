<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $domains = $user->domains()
            ->with('latestCheck')
            ->withCount('checks')
            ->latest()
            ->get();

        $stats = [
            'total'   => $domains->count(),
            'online'  => $domains->filter(fn ($d) => $d->latestCheck?->status === 'online')->count(),
            'offline' => $domains->filter(fn ($d) => $d->latestCheck?->status === 'offline')->count(),
            'pending' => $domains->filter(fn ($d) => $d->latestCheck === null && $d->is_active)->count(),
        ];

        $recentChecks = \App\Models\DomainCheck::query()
            ->whereIn('domain_id', $domains->pluck('id'))
            ->with('domain')
            ->latest()
            ->limit(10)
            ->get();

        return view('dashboard.index', compact('domains', 'stats', 'recentChecks'));
    }
}
