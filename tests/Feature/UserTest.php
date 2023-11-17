<?php

namespace Tests\Feature;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;

use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_user()
    {
        $userData = [
            'name' => 'Hossein Sattari',
            'email' => 'hossein@example.com',
            'password' => Hash::make('password123'),
        ];

        $userRepo = new UserRepository(new User());
        $user = $userRepo->create($userData);

        $this->assertDatabaseHas('users', ['email' => 'hossein@example.com']);
        $this->assertEquals('Hossein Sattari', $user->name);
    }

    public function test_can_create_user_via_register_route()
    {
        $userData = [
            'name' => 'Hossein Sattari',
            'email' => 'hossein@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->post(route('register'), $userData);
        $response->assertStatus(302);

        $this->assertDatabaseHas('users', ['email' => 'hossein@example.com']);
        $this->assertDatabaseHas('users', ['name' => 'Hossein Sattari']);
    }

    public function test_email_is_required()
    {
        $response = $this->post(route('register'), [
            'name' => 'Hossein Sattari',
            'email' => '',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_email_should_be_unique()
    {
        $user1 = User::factory()->create(['email' => 'hossein@example.com']);

        $response = $this->post(route('register'), [
            'name' => 'Jane Doe',
            'email' => 'hossein@example.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_password_is_required()
    {
        $response = $this->post(route('register'), [
            'name' => 'Hossein Sattari',
            'email' => 'hossein@example.com',
            'password' => '',
        ]);

        $response->assertSessionHasErrors('password');
    }
}
