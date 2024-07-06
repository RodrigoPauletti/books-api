<?php

namespace Tests\Unit;

use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

use App\Models\User;

class AuthTokenTest extends TestCase
{

    #[Test]
    public function it_can_retrieve_a_token(): void {
        $user = User::factory()->create();

        // Arrange
        $credentials = [
            'email' => $user->email,
            'password' => 'password'
        ];

        // Act
        $response = $this->postJson('/api/v1/auth/token', $credentials);

        // Assert
        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'access_token',
                'token_type'
            ]);
    }
}
