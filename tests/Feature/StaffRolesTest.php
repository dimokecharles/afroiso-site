<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaffRolesTest extends TestCase
{
    use RefreshDatabase;

    private function staff(string $role, array $extra = []): User
    {
        return User::factory()->create(['role' => $role] + $extra);
    }

    public function test_each_role_opens_only_its_own_areas(): void
    {
        $this->seed();
        $pages = ['/admin/contacts', '/admin/enquiries', '/admin/enrolments', '/admin/orders', '/admin/projects', '/admin/courses', '/admin/certificates', '/admin/staff'];
        $allowed = [
            'admin' => $pages,
            'finance' => ['/admin/contacts', '/admin/enrolments', '/admin/orders', '/admin/projects'],
            'sales' => ['/admin/contacts', '/admin/enquiries', '/admin/enrolments', '/admin/projects'],
            'content' => ['/admin/courses', '/admin/certificates'],
        ];

        foreach ($allowed as $role => $open) {
            $person = $this->staff($role);
            foreach ($pages as $page) {
                $this->actingAs($person)->get($page)->assertStatus(in_array($page, $open, true) ? 200 : 403);
            }
            $this->actingAs($person)->get('/admin/account')->assertOk();
        }
    }

    public function test_dashboard_hides_money_from_sales_and_skips_content_editors(): void
    {
        $this->actingAs($this->staff('sales'))->get('/admin')->assertOk()->assertDontSee('Revenue this month')->assertSee('New enquiries');
        $this->actingAs($this->staff('finance'))->get('/admin')->assertOk()->assertSee('Revenue this month')->assertDontSee('Latest enquiries');
        $this->actingAs($this->staff('content'))->get('/admin')->assertRedirect('/admin/courses');
    }

    public function test_administrator_manages_staff_logins(): void
    {
        $admin = $this->staff('admin');

        $this->actingAs($admin)->post('/admin/staff', ['name' => 'Sales Person', 'email' => 'Sales@Example.com', 'role' => 'sales', 'is_active' => '1', 'password' => 'a-long-password'])->assertRedirect('/admin/staff');
        $person = User::where('email', 'sales@example.com')->firstOrFail();
        $this->assertSame('sales', $person->role);

        $this->actingAs($admin)->post('/admin/staff', ['name' => 'Short', 'email' => 'short@example.com', 'role' => 'sales', 'password' => 'short'])->assertSessionHasErrors('password');
        $this->actingAs($admin)->put('/admin/staff/' . $person->id, ['name' => 'Sales Person', 'email' => 'sales@example.com', 'role' => 'finance', 'is_active' => '1'])->assertRedirect('/admin/staff');
        $this->assertSame('finance', $person->fresh()->role);

        auth()->logout();
        $this->post('/admin/login', ['email' => 'sales@example.com', 'password' => 'a-long-password'])->assertRedirect('/admin');
    }

    public function test_switched_off_login_stops_working(): void
    {
        $person = $this->staff('sales', ['email' => 'off@example.com', 'password' => 'a-long-password', 'is_active' => false]);

        $this->post('/admin/login', ['email' => 'off@example.com', 'password' => 'a-long-password']);
        $this->assertGuest();

        $this->actingAs($person)->get('/admin/contacts')->assertRedirect('/admin/login');
        $this->assertGuest();
    }

    public function test_the_last_administrator_cannot_be_removed(): void
    {
        $admin = $this->staff('admin');
        $this->actingAs($admin)->put('/admin/staff/' . $admin->id, ['name' => $admin->name, 'email' => $admin->email, 'role' => 'sales', 'is_active' => '1'])->assertSessionHasErrors();
        $this->assertSame('admin', $admin->fresh()->role);

        $second = $this->staff('admin');
        $this->actingAs($admin)->put('/admin/staff/' . $second->id, ['name' => $second->name, 'email' => $second->email, 'role' => 'sales', 'is_active' => '1'])->assertRedirect('/admin/staff');
        $this->assertSame('sales', $second->fresh()->role);
    }

    public function test_staff_change_their_own_password(): void
    {
        $person = $this->staff('sales', ['email' => 'me@example.com', 'password' => 'old-long-password']);

        $this->actingAs($person)->put('/admin/account', ['current_password' => 'wrong', 'password' => 'new-long-password', 'password_confirmation' => 'new-long-password'])->assertSessionHasErrors('current_password');
        $this->actingAs($person)->put('/admin/account', ['current_password' => 'old-long-password', 'password' => 'new-long-password', 'password_confirmation' => 'new-long-password'])->assertSessionHasNoErrors();

        auth()->logout();
        $this->post('/admin/login', ['email' => 'me@example.com', 'password' => 'new-long-password'])->assertRedirect('/admin');
    }
}
