<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use App\Models\User;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_logout_001(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123')
        ]);
        $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123'
        ]);
        $this->assertTrue(auth()->check());
        $response = $this->post('/logout');
        $this->assertFalse(auth()->check());
        $response->assertRedirect('/');
    }
}
