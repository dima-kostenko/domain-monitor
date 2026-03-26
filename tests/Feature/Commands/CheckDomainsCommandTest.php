<?php

namespace Tests\Feature\Commands;

use App\Models\Domain;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CheckDomainsCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_checks_all_due_active_domains(): void
    {
        $user = User::factory()->create();
        $domains = Domain::factory(3)->create([
            'user_id'   => $user->id,
            'is_active' => true,
        ]);

        Http::fake(fn () => Http::response('', 200));

        $this->artisan('check:domains --force')
            ->assertSuccessful();

        foreach ($domains as $domain) {
            $this->assertDatabaseHas('domain_checks', ['domain_id' => $domain->id]);
        }
    }

    public function test_command_skips_inactive_domains(): void
    {
        $user = User::factory()->create();
        $domain = Domain::factory()->create(['user_id' => $user->id, 'is_active' => false]);

        Http::fake(fn () => Http::response('', 200));

        $this->artisan('check:domains --force')
            ->assertSuccessful();

        $this->assertDatabaseMissing('domain_checks', ['domain_id' => $domain->id]);
    }

    public function test_command_checks_single_domain_by_id(): void
    {
        $user = User::factory()->create();
        $target = Domain::factory()->create(['user_id' => $user->id, 'is_active' => true]);
        $other  = Domain::factory()->create(['user_id' => $user->id, 'is_active' => true]);

        Http::fake(fn () => Http::response('', 200));

        $this->artisan("check:domains --domain={$target->id}")
            ->assertSuccessful();

        $this->assertDatabaseHas('domain_checks', ['domain_id' => $target->id]);
        $this->assertDatabaseMissing('domain_checks', ['domain_id' => $other->id]);
    }

    public function test_command_respects_check_interval(): void
    {
        $user = User::factory()->create();

        // Domain checked 2 minutes ago, interval is 5 min → should NOT check
        $domain = Domain::factory()->create([
            'user_id'        => $user->id,
            'is_active'      => true,
            'check_interval' => 5,
        ]);
        $domain->checks()->create([
            'status'       => 'online',
            'response_code' => 200,
            'response_time' => 100,
            'created_at'   => now()->subMinutes(2),
        ]);

        Http::fake(fn () => Http::response('', 200));

        $this->artisan('check:domains')
            ->assertSuccessful();

        // Still only 1 check in DB
        $this->assertCount(1, $domain->checks);
    }
}
