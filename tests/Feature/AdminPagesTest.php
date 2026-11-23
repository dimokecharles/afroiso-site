<?php

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\Enquiry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_every_admin_list_opens(): void
    {
        $this->seed();
        $admin = User::factory()->create();

        foreach (['/admin', '/admin/enquiries', '/admin/courses', '/admin/publications', '/admin/certificates', '/admin/orders', '/admin/contacts', '/admin/contacts/create', '/admin/enrolments', '/admin/orders/create'] as $url) {
            $this->actingAs($admin)->get($url)->assertOk();
        }
    }

    public function test_long_lists_show_page_links(): void
    {
        $admin = User::factory()->create();

        for ($i = 1; $i <= 35; $i++) {
            Enquiry::create(['name' => 'Person ' . $i, 'email' => 'person' . $i . '@example.com', 'message' => 'Hello']);
            Certificate::create(['number' => 'TEST-' . $i, 'holder_name' => 'Person ' . $i, 'course_title' => 'Course', 'issued_on' => '2027-03-01', 'status' => 'valid']);
        }

        $this->actingAs($admin)->get('/admin/enquiries')->assertOk()->assertSee('Next');
        $this->actingAs($admin)->get('/admin/enquiries?page=2')->assertOk()->assertSee('Previous');
        $this->actingAs($admin)->get('/admin/certificates')->assertOk()->assertSee('Next');
    }

    public function test_admin_password_can_be_reset_from_the_settings_file(): void
    {
        config(['afroiso.admin_email' => 'admin@example.com', 'afroiso.admin_password' => 'first-password']);
        $this->artisan('afroiso:sync-admin')->assertExitCode(0);
        $this->post('/admin/login', ['email' => 'admin@example.com', 'password' => 'first-password'])->assertRedirect('/admin');
        auth()->logout();

        config(['afroiso.admin_password' => 'second-password']);
        $this->artisan('afroiso:sync-admin')->assertExitCode(0);
        $this->assertSame(1, User::where('email', 'admin@example.com')->count());
        $this->post('/admin/login', ['email' => 'admin@example.com', 'password' => 'first-password']);
        $this->assertGuest();
        $this->post('/admin/login', ['email' => 'admin@example.com', 'password' => 'second-password'])->assertRedirect('/admin');
    }
}
