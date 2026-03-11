<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    use RefreshDatabase;

    protected UserService $userService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userService = app(UserService::class);
    }

    public function test_can_create_client_user()
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'Secret123',
        ];

        $user = $this->userService->createUser($data);
        $user->refresh();

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('John Doe', $user->name);
        $this->assertEquals('john@example.com', $user->email);
        $this->assertEquals('client', $user->role);
        $this->assertTrue((bool)$user->is_active);
        $this->assertEquals(0, $user->balance);
        $this->assertTrue(Hash::check('Secret123', $user->password));

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
            'role' => 'client',
            'is_active' => true,
        ]);
    }

    public function test_can_create_admin_user()
    {
        $data = [
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => 'Secret123',
            'role' => 'admin',
        ];

        $user = $this->userService->createUser($data);

        $this->assertEquals('admin', $user->role);
        $this->assertDatabaseHas('users', [
            'email' => 'admin@example.com',
            'role' => 'admin',
        ]);
    }

    public function test_can_update_user_details()
    {
        $user = User::factory()->create([
            'name' => 'Old Name',
            'email' => 'old@example.com',
        ]);

        $this->userService->updateUser($user->id, [
            'name' => 'New Name',
            'email' => 'new@example.com',
            'is_active' => false,
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'New Name',
            'email' => 'new@example.com',
            'is_active' => false,
        ]);
    }

    public function test_can_update_user_password()
    {
        $user = User::factory()->create([
            'password' => 'OldPassword123',
        ]);

        $this->userService->updateUser($user->id, [
            'password' => 'NewPassword456',
        ]);

        $user->refresh();

        $this->assertTrue(Hash::check('NewPassword456', $user->password));
        $this->assertFalse(Hash::check('OldPassword123', $user->password));
    }

    public function test_can_get_dashboard_stats()
    {
        // Administrador
        User::factory()->create(['role' => 'admin', 'is_active' => true]);
        
        // 2 Clientes ativos, 1 inativo
        User::factory()->count(2)->create(['role' => 'client', 'is_active' => true]);
        User::factory()->create(['role' => 'client', 'is_active' => false]);

        $stats = $this->userService->getDashboardStats();

        $this->assertEquals(4, $stats['total_users']);
        $this->assertEquals(3, $stats['active_users']);
    }
}
