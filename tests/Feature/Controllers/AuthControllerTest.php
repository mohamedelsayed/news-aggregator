<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_registers_a_user()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertCreated()
            ->assertJson([
                'message' => 'User registered successfully',
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
        ]);
    }

    /** @test */
    public function it_logs_in_a_user_with_valid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'login@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'login@example.com',
            'password' => 'password123',
        ]);

        $response->assertOk()
            ->assertJson([
                'message' => 'User logged in successfully',
            ]);
    }

    /** @test */
    public function it_fails_login_with_invalid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'fail@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'fail@example.com',
            'password' => 'wrongpass',
        ]);

        $response->assertUnauthorized()
            ->assertJson([
                'message' => 'Invalid credentials',
            ]);
    }

    /** @test */
    public function it_logs_out_a_user()
    {
        $user = User::factory()->create();
        $token = $user->createToken('api-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/logout');

        $response->assertOk()
            ->assertJson([
                'message' => 'Logged out successfully',
            ]);
    }

    /** @test */
    public function it_sends_a_password_reset_link()
    {
        Notification::fake();
        $user = User::factory()->create([
            'email' => 'reset@example.com',
        ]);

        $response = $this->postJson('/api/forgot-password', [
            'email' => 'reset@example.com',
        ]);

        $response->assertOk()
            ->assertJson([
                'message' => 'We have emailed your password reset link.',
            ]);

        Notification::assertSentTo($user, ResetPassword::class);
    }

    /** @test */
    public function it_resets_password_with_valid_token()
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'reset2@example.com',
        ]);

        // Simulate sending reset link
        $token = Password::createToken($user);

        $response = $this->postJson('/api/reset-password', [
            'email' => 'reset2@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
            'token' => $token,
        ]);

        $response->assertOk()
            ->assertJson([
                'message' => 'Your password has been reset.',
            ]);

        $this->assertTrue(Hash::check('newpassword123', $user->fresh()->password));
    }

    /** @test */
    public function it_fails_reset_password_with_invalid_token()
    {
        $user = User::factory()->create([
            'email' => 'failreset@example.com',
        ]);

        $response = $this->postJson('/api/reset-password', [
            'email' => 'failreset@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
            'token' => 'invalid-token',
        ]);

        $response->assertBadRequest()
            ->assertJson([
                'message' => 'This password reset token is invalid.',
            ]);
    }
}
