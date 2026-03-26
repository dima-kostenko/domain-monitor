<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDomainRequest;
use App\Http\Requests\UpdateDomainRequest;
use App\Models\Domain;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DomainController extends Controller
{
    public function index(Request $request): View
    {
        $domains = $request->user()
            ->domains()
            ->with('latestCheck')
            ->withCount('checks')
            ->latest()
            ->paginate(15);

        return view('domains.index', compact('domains'));
    }

    public function create(): View
    {
        return view('domains.create', [
            'domain' => new Domain([
                'check_interval' => 5,
                'timeout'        => 10,
                'method'         => 'HEAD',
                'is_active'      => true,
            ]),
        ]);
    }

    public function store(StoreDomainRequest $request): RedirectResponse
    {
        $domain = $request->user()->domains()->create($request->validated());

        return redirect()
            ->route('domains.show', $domain)
            ->with('success', "Domain «{$domain->name}» added and will be checked shortly.");
    }

    public function show(Request $request, Domain $domain): View
    {
        $this->authorizeOwner($request, $domain);

        $checks = $domain->checks()
            ->latest()
            ->paginate(20);

        $stats = [
            'total'      => $domain->checks()->count(),
            'online'     => $domain->checks()->where('status', 'online')->count(),
            'avg_time'   => $domain->checks()->where('status', 'online')->avg('response_time'),
        ];

        $stats['uptime'] = $stats['total'] > 0
            ? round($stats['online'] / $stats['total'] * 100, 2)
            : null;

        return view('domains.show', compact('domain', 'checks', 'stats'));
    }

    public function edit(Request $request, Domain $domain): View
    {
        $this->authorizeOwner($request, $domain);

        return view('domains.edit', compact('domain'));
    }

    public function update(UpdateDomainRequest $request, Domain $domain): RedirectResponse
    {
        $domain->update($request->validated());

        return redirect()
            ->route('domains.show', $domain)
            ->with('success', "Domain «{$domain->name}» updated.");
    }

    public function destroy(Request $request, Domain $domain): RedirectResponse
    {
        $this->authorizeOwner($request, $domain);

        $name = $domain->name;
        $domain->delete();

        return redirect()
            ->route('domains.index')
            ->with('success', "Domain «{$name}» deleted.");
    }

    // ─── Private ─────────────────────────────────────────────────────────────

    private function authorizeOwner(Request $request, Domain $domain): void
    {
        if ($request->user()->id !== $domain->user_id && ! $request->user()->isAdmin()) {
            abort(403);
        }
    }
}
