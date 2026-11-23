<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\Enquiry;
use App\Models\Order;
use App\Models\PageView;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CrmTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->admin = User::factory()->create(['name' => 'Office Admin']);
    }

    public function test_enquiry_and_order_from_one_email_share_a_contact(): void
    {
        $this->post('/contact', ['name' => 'Rudo Moyo', 'email' => 'Rudo@Example.com', 'phone' => '+263 77 000 0000', 'message' => 'Please send dates.']);
        $this->post('/courses/iso-9001-quality-management/enrol', ['customer_name' => 'Rudo Moyo', 'customer_email' => 'rudo@example.com', 'accept' => '1']);

        $this->assertSame(1, Contact::count());
        $contact = Contact::firstOrFail();
        $this->assertSame('rudo@example.com', $contact->email);
        $this->assertSame('+263 77 000 0000', $contact->phone);
        $this->assertSame($contact->id, Enquiry::firstOrFail()->contact_id);
        $this->assertSame($contact->id, Order::firstOrFail()->contact_id);

        $this->actingAs($this->admin)->get('/admin/contacts/' . $contact->id)
            ->assertOk()->assertSee('Rudo Moyo')->assertSee('Enquiry:')->assertSee('Course enrolment:');
        $this->actingAs($this->admin)->get('/admin/contacts?q=rudo')->assertOk()->assertSee('rudo@example.com');
        $this->actingAs($this->admin)->get('/admin/contacts?q=nobody-here')->assertOk()->assertDontSee('rudo@example.com');
    }

    public function test_lead_can_be_moved_along_and_noted(): void
    {
        $enquiry = Enquiry::create(['name' => 'Tariro', 'email' => 'tariro@example.com', 'message' => 'Hello']);
        $this->assertSame('new', $enquiry->fresh()->status);

        $this->actingAs($this->admin)->put('/admin/enquiries/' . $enquiry->id, [
            'status' => 'qualified', 'assigned_to' => $this->admin->id, 'follow_up_on' => '2027-01-10',
        ])->assertRedirect();

        $enquiry->refresh();
        $this->assertSame('qualified', $enquiry->status);
        $this->assertSame($this->admin->id, $enquiry->assigned_to);
        $this->assertSame('2027-01-10', $enquiry->follow_up_on->toDateString());

        $this->actingAs($this->admin)->post('/admin/notes', ['on' => 'enquiry', 'id' => $enquiry->id, 'body' => 'Called, wants the January intake.'])->assertRedirect();
        $this->actingAs($this->admin)->get('/admin/enquiries/' . $enquiry->id)->assertOk()->assertSee('Called, wants the January intake.')->assertSee('Office Admin');
        $this->actingAs($this->admin)->get('/admin/enquiries?status=qualified')->assertOk()->assertSee('Tariro');
        $this->actingAs($this->admin)->get('/admin/enquiries?status=closed')->assertOk()->assertDontSee('Tariro');
    }

    public function test_contact_can_be_added_edited_and_noted_by_staff(): void
    {
        $this->actingAs($this->admin)->post('/admin/contacts', ['name' => 'Farai Dube', 'email' => 'farai@example.com', 'organisation' => 'Dube Foods'])->assertRedirect();
        $contact = Contact::where('email', 'farai@example.com')->firstOrFail();

        $this->actingAs($this->admin)->put('/admin/contacts/' . $contact->id, ['name' => 'Farai Dube', 'email' => 'farai@example.com', 'country' => 'Zimbabwe'])->assertRedirect();
        $this->assertSame('Zimbabwe', $contact->fresh()->country);

        $this->actingAs($this->admin)->post('/admin/notes', ['on' => 'contact', 'id' => $contact->id, 'body' => 'Met at the trade fair.'])->assertRedirect();
        $this->actingAs($this->admin)->get('/admin/contacts/' . $contact->id)->assertOk()->assertSee('Met at the trade fair.');
    }

    public function test_dashboard_reports_revenue_and_enrolments(): void
    {
        $this->post('/courses/iso-9001-quality-management/enrol', ['customer_name' => 'Paid Person', 'customer_email' => 'paid@example.com', 'accept' => '1']);
        Order::firstOrFail()->markPaid('manual');
        $this->post('/courses/internal-auditor/enrol', ['customer_name' => 'Waiting Person', 'customer_email' => 'waiting@example.com', 'accept' => '1']);

        $this->actingAs($this->admin)->get('/admin')
            ->assertOk()->assertSee('US$500')->assertSee('Best-selling courses')->assertSee('ISO 9001:2026 Quality Management Systems (QMS)')->assertSee('Worth US$250');
        $this->actingAs($this->admin)->get('/admin/enrolments')->assertOk()->assertSee('Paid Person')->assertSee('Waiting Person');
        $this->actingAs($this->admin)->get('/admin/enrolments?status=paid')->assertOk()->assertSee('Paid Person')->assertDontSee('Waiting Person');
    }

    public function test_public_page_visits_are_counted_without_personal_details(): void
    {
        $this->get('/about', ['Referer' => 'https://www.google.com/search?q=iso'])->assertOk();
        $this->get('/courses')->assertOk();
        $this->actingAs($this->admin)->get('/admin')->assertOk();

        $this->assertSame(2, PageView::count());
        $view = PageView::where('path', '/about')->firstOrFail();
        $this->assertSame('www.google.com', $view->referrer_host);
        $this->assertSame(64, strlen($view->visitor));
        $this->assertFalse(PageView::where('path', 'like', '/admin%')->exists());
    }
}
