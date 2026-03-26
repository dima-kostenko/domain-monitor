<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_is_shown_to_guests(): void
    {
        $this->get(route('login'))->assertOk();
    }

    public function test_authenticated_user_is_redirected_from_login(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('login'))
            ->assertRedirect(route('dashboard'));
    }

    public function test_user_can_login_with_correct_credentials(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('correct-password'),
        ]);

        $this->post(route('login'), [
            'email'    => $user->email,
            'password' => 'correct-password',
        ])
        ->assertRedirect(route('domains.index'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_login_fails_with_wrong_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('correct-password'),
        ]);

        $this->post(route('login'), [
            'email'    => $user->email,
            'password' => 'wrong-password',
        ])
        ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_login_fails_for_unknown_email(): void
    {
        $this->post(route('login'), [
            'email'    => 'ghost@example.com',
            'password' => 'anything',
        ])
        ->assertSessionHasErrors('email');
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
        $this->get(route('domains.index'))->assertRedirect(route('login'));
    }
}
