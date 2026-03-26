<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_accessible(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('profile.edit'))
            ->assertOk();
    }

    public function test_user_can_update_name_and_email(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->patch(route('profile.update'), [
                'name'  => 'New Name',
                'email' => 'new@example.com',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('users', [
            'id'    => $user->id,
            'name'  => 'New Name',
            'email' => 'new@example.com',
        ]);
    }

    public function test_email_must_be_unique_on_update(): void
    {
        $other = User::factory()->create(['email' => 'taken@example.com']);
        $user  = User::factory()->create();

        $this->actingAs($user)
            ->patch(route('profile.update'), [
                'name'  => 'Test',
                'email' => 'taken@example.com',
            ])
            ->assertSessionHasErrors('email');
    }

    public function test_user_can_change_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('old-password1'),
        ]);

        $this->actingAs($user)
            ->patch(route('profile.password'), [
                'current_password'      => 'old-password1',
                'password'              => 'new-password2',
                'password_confirmation' => 'new-password2',
            ])
            ->assertRedirect();

        $this->assertTrue(Hash::check('new-password2', $user->fresh()->password));
    }

    public function test_wrong_current_password_is_rejected(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('real-password1'),
        ]);

        $this->actingAs($user)
            ->patch(route('profile.password'), [
                'current_password'      => 'wrong-password',
                'password'              => 'new-password2',
                'password_confirmation' => 'new-password2',
            ])
            ->assertSessionHasErrors('current_password');
    }

    public function test_user_can_delete_own_account(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('my-password1'),
        ]);

        $this->actingAs($user)
            ->delete(route('profile.destroy'), ['password' => 'my-password1'])
            ->assertRedirect(route('login'));

        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }
}
