<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTypeRegistrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_register_as_legal_professional()
    {
        $userData = [
            'name' => 'Test Legal Professional',
            'email' => 'legal@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'user_type' => 'legal_professional',
        ];

        $response = $this->post(route('register'), $userData);

        $this->assertDatabaseHas('users', [
            'email' => 'legal@test.com',
            'user_type' => 'legal_professional',
        ]);

        $user = User::where('email', 'legal@test.com')->first();
        $this->assertTrue($user->isLegalProfessional());
        $this->assertFalse($user->isProSe());
    }

    /** @test */
    public function user_can_register_as_pro_se()
    {
        $userData = [
            'name' => 'Test Pro-Se User',
            'email' => 'prose@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'user_type' => 'pro_se',
        ];

        $response = $this->post(route('register'), $userData);

        $this->assertDatabaseHas('users', [
            'email' => 'prose@test.com',
            'user_type' => 'pro_se',
        ]);

        $user = User::where('email', 'prose@test.com')->first();
        $this->assertTrue($user->isProSe());
        $this->assertFalse($user->isLegalProfessional());
    }

    /** @test */
    public function registration_requires_user_type()
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            // user_type is missing
        ];

        $response = $this->post(route('register'), $userData);

        $response->assertSessionHasErrors('user_type');
        $this->assertDatabaseMissing('users', [
            'email' => 'test@test.com',
        ]);
    }

    /** @test */
    public function user_type_must_be_valid_value()
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'user_type' => 'invalid_type',
        ];

        $response = $this->post(route('register'), $userData);

        $response->assertSessionHasErrors('user_type');
        $this->assertDatabaseMissing('users', [
            'email' => 'test@test.com',
        ]);
    }

    /** @test */
    public function user_type_display_attribute_works_correctly()
    {
        $legalUser = User::factory()->create(['user_type' => 'legal_professional']);
        $proSeUser = User::factory()->create(['user_type' => 'pro_se']);

        $this->assertEquals('Legal Professional', $legalUser->user_type_display);
        $this->assertEquals('Pro-Se Litigant', $proSeUser->user_type_display);
    }
}
