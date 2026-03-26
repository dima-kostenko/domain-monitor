<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_is_shown_to_guests(): void
    {
        $this->get(route('register'))->assertOk();
    }

    public function test_authenticated_user_is_redirected_from_register(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('register'))
            ->assertRedirect(route('dashboard'));
    }

    public function test_user_can_register_with_valid_data(): void
    {
        $this->post(route('register'), [
            'name'                  => 'Jane Doe',
            'email'                 => 'jane@example.com',
            'password'              => 'secret123',
            'password_confirmation' => 'secret123',
        ])
        ->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'jane@example.com']);
    }

    public function test_registration_fails_with_duplicate_email(): void
    {
        User::factory()->create(['email' => 'taken@example.com']);

        $this->post(route('register'), [
            'name'                  => 'Someone',
            'email'                 => 'taken@example.com',
            'password'              => 'secret123',
            'password_confirmation' => 'secret123',
        ])
        ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_registration_fails_with_weak_password(): void
    {
        $this->post(route('register'), [
            'name'                  => 'Joe',
            'email'                 => 'joe@example.com',
            'password'              => '12345678',   // no letters
            'password_confirmation' => '12345678',
        ])
        ->assertSessionHasErrors('password');
    }

    public function test_registration_fails_when_passwords_dont_match(): void
    {
        $this->post(route('register'), [
            'name'                  => 'Joe',
            'email'                 => 'joe@example.com',
            'password'              => 'secret123',
            'password_confirmation' => 'different1',
        ])
        ->assertSessionHasErrors('password');
    }
}
