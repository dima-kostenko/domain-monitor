<?php

namespace Tests\Feature\Http;

use App\Models\Domain;
use App\Models\DomainCheck;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DomainCheckControllerTest extends TestCase
{
    use RefreshDatabase;

    private User   $user;
    private Domain $domain;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user   = User::factory()->create();
        $this->domain = Domain::factory()->create(['user_id' => $this->user->id]);
    }

    // ─── Access control ──────────────────────────────────────────────────────

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('domain-checks.index', $this->domain))
            ->assertRedirect(route('login'));
    }

    public function test_owner_can_view_checks(): void
    {
        $this->actingAs($this->user)
            ->get(route('domain-checks.index', $this->domain))
            ->assertOk();
    }

    public function test_other_user_receives_403(): void
    {
        $other = User::factory()->create();

        $this->actingAs($other)
            ->get(route('domain-checks.index', $this->domain))
            ->assertForbidden();
    }

    public function test_admin_can_view_any_domain_checks(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->get(route('domain-checks.index', $this->domain))
            ->assertOk();
    }

    // ─── Filtering ───────────────────────────────────────────────────────────

    public function test_filter_by_online_status(): void
    {
        DomainCheck::factory()->online()->create(['domain_id' => $this->domain->id]);
        DomainCheck::factory()->offline()->create(['domain_id' => $this->domain->id]);

        $response = $this->actingAs($this->user)
            ->get(route('domain-checks.index', $this->domain) . '?status=online');

        $response->assertOk();

        // Stats should reflect only online checks
        $response->assertViewHas('stats', fn ($s) => $s['online'] === 1 && $s['offline'] === 0);
    }

    public function test_filter_by_offline_status(): void
    {
        DomainCheck::factory()->online()->create(['domain_id' => $this->domain->id]);
        DomainCheck::factory()->offline()->count(2)->create(['domain_id' => $this->domain->id]);

        $response = $this->actingAs($this->user)
            ->get(route('domain-checks.index', $this->domain) . '?status=offline');

        $response->assertViewHas('stats', fn ($s) => $s['offline'] === 2 && $s['online'] === 0);
    }

    public function test_filter_by_date_from(): void
    {
        DomainCheck::factory()->create([
            'domain_id'  => $this->domain->id,
            'created_at' => now()->subDays(10),
        ]);
        DomainCheck::factory()->create([
            'domain_id'  => $this->domain->id,
            'created_at' => now()->subDays(1),
        ]);

        $dateFrom = now()->subDays(3)->toDateString();

        $response = $this->actingAs($this->user)
            ->get(route('domain-checks.index', $this->domain) . "?date_from={$dateFrom}");

        $response->assertViewHas('stats', fn ($s) => $s['total'] === 1);
    }

    public function test_filter_by_date_range(): void
    {
        DomainCheck::factory()->count(3)->create([
            'domain_id'  => $this->domain->id,
            'created_at' => now()->subDays(5),
        ]);
        DomainCheck::factory()->count(2)->create([
            'domain_id'  => $this->domain->id,
            'created_at' => now(),
        ]);

        $from = now()->subDays(7)->toDateString();
        $to   = now()->subDays(3)->toDateString();

        $response = $this->actingAs($this->user)
            ->get(route('domain-checks.index', $this->domain) . "?date_from={$from}&date_to={$to}");

        $response->assertViewHas('stats', fn ($s) => $s['total'] === 3);
    }

    public function test_invalid_status_filter_is_rejected(): void
    {
        $this->actingAs($this->user)
            ->get(route('domain-checks.index', $this->domain) . '?status=unknown')
            ->assertSessionHasErrors('status');
    }

    public function test_future_date_from_is_rejected(): void
    {
        $this->actingAs($this->user)
            ->get(route('domain-checks.index', $this->domain) . '?date_from=' . now()->addDay()->toDateString())
            ->assertSessionHasErrors('date_from');
    }

    // ─── Pagination ───────────────────────────────────────────────────────────

    public function test_per_page_option_is_respected(): void
    {
        DomainCheck::factory()->count(60)->create(['domain_id' => $this->domain->id]);

        $response = $this->actingAs($this->user)
            ->get(route('domain-checks.index', $this->domain) . '?per_page=50');

        $response->assertViewHas('checks', fn ($p) => $p->perPage() === 50);
    }

    public function test_invalid_per_page_is_rejected(): void
    {
        $this->actingAs($this->user)
            ->get(route('domain-checks.index', $this->domain) . '?per_page=999')
            ->assertSessionHasErrors('per_page');
    }
}
