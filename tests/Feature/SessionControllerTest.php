<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SessionControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_registration_creates_user_and_signs_in(): void
    {
        $response = $this->post(route('register.store'), ['name' => 'Светлана', 'email' => 'SVETA@example.com', 'password' => 'secret1234', 'password_confirmation' => 'secret1234']);
        $response->assertRedirect(route('diary'));
        $this->assertDatabaseHas('users', ['name' => 'Светлана', 'email' => 'sveta@example.com']);
        $user = User::firstOrFail();
        $this->assertAuthenticatedAs($user);
        $this->assertTrue(Hash::check('secret1234', $user->password));
    }

    public function test_registration_rejects_duplicate_email_ignoring_case(): void
    {
        User::factory()->create(['email' => 'sveta@example.com']);
        $response = $this->post(route('register.store'), ['name' => 'Светлана', 'email' => 'SVETA@example.com', 'password' => 'secret1234', 'password_confirmation' => 'secret1234']);
        $response->assertSessionHasErrors(['email' => 'Эта почта уже зарегистрирована.']);
        $this->assertDatabaseCount('users', 1);
        $this->assertGuest();
    }

    public function test_registration_rejects_password_confirmation_mismatch(): void
    {
        $response = $this->post(route('register.store'), ['name' => 'Анна', 'email' => 'new@example.com', 'password' => 'secret1234', 'password_confirmation' => 'another1234']);
        $response->assertSessionHasErrors(['password' => 'Пароли не совпадают.']);
        $this->assertDatabaseCount('users', 0);
    }

    public function test_login_accepts_valid_credentials_and_uppercase_email(): void
    {
        $user = User::factory()->create(['email' => 'anna@example.com']);
        $response = $this->post(route('login.store'), ['email' => 'ANNA@example.com', 'password' => 'Travel2026!']);
        $response->assertRedirect(route('diary'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_rejects_wrong_password(): void
    {
        User::factory()->create(['email' => 'anna@example.com']);
        $response = $this->post(route('login.store'), ['email' => 'anna@example.com', 'password' => 'wrong']);
        $response->assertSessionHasErrors(['email' => 'Неверная почта или пароль.']);
        $this->assertGuest();
    }

    public function test_logout_ends_authenticated_session(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->post(route('logout'));
        $response->assertRedirect(route('trips.index'));
        $this->assertGuest();
    }
}
