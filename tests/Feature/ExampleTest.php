<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_pages_load(): void
    {
        $this->seed();

        foreach (['/', '/about', '/courses', '/courses/internal-auditor', '/courses/iso-9001-quality-management', '/consulting', '/publications', '/verify', '/verify?number=NONE', '/contact', '/privacy-policy', '/sitemap.xml'] as $url) {
            $this->get($url)->assertStatus(200);
        }
    }

    public function test_old_lead_auditor_address_redirects(): void
    {
        $this->get('/courses/lead-auditor')->assertRedirect('/courses/internal-auditor');
    }

    public function test_enquiry_is_saved(): void
    {
        $this->post('/contact', [
            'name' => 'Test Person',
            'email' => 'test@example.com',
            'message' => 'Please send me the course dates.',
        ])->assertRedirect('/contact');

        $this->assertDatabaseHas('enquiries', ['email' => 'test@example.com']);
    }

    public function test_admin_requires_login(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
    }
}
