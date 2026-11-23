<?php

namespace Tests\Feature;

use App\Mail\EnquiryReply;
use App\Models\Enquiry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class MessageBoxTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Enquiry $enquiry;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['name' => 'Office Admin']);
        $this->post('/contact', ['name' => 'Rudo Moyo', 'email' => 'rudo@example.com', 'subject' => 'Enrol: Internal Auditor', 'message' => 'When does the next course start?']);
        $this->enquiry = Enquiry::firstOrFail();
    }

    public function test_new_enquiry_waits_in_the_message_box(): void
    {
        $this->assertTrue($this->enquiry->awaiting_reply);
        $this->actingAs($this->admin)->get('/admin/messages')->assertOk()->assertSee('Rudo Moyo')->assertSee('Waiting for a reply (1)');
        $this->actingAs($this->admin)->get('/admin/enquiries/' . $this->enquiry->id)->assertOk()->assertSee('When does the next course start?')->assertSee('Send reply');
    }

    public function test_reply_is_emailed_and_kept_in_the_conversation(): void
    {
        Mail::fake();
        config(['mail.default' => 'smtp']);

        $this->actingAs($this->admin)->post('/admin/enquiries/' . $this->enquiry->id . '/reply', ['body' => 'The next intake starts on 15 January 2027.'])->assertRedirect();

        Mail::assertSent(EnquiryReply::class, fn (EnquiryReply $mail) => $mail->hasTo('rudo@example.com') && $mail->reply->body === 'The next intake starts on 15 January 2027.');

        $this->enquiry->refresh();
        $this->assertFalse($this->enquiry->awaiting_reply);
        $this->assertSame('contacted', $this->enquiry->status);
        $message = $this->enquiry->messages()->firstOrFail();
        $this->assertSame('out', $message->direction);
        $this->assertSame('sent', $message->delivery);

        $this->actingAs($this->admin)->get('/admin/enquiries/' . $this->enquiry->id)->assertOk()->assertSee('The next intake starts on 15 January 2027.')->assertSee('Emailed');
        $this->actingAs($this->admin)->get('/admin/messages')->assertOk()->assertSee('Nothing is waiting for a reply.');
        $this->actingAs($this->admin)->get('/admin/messages?show=all')->assertOk()->assertSee('Rudo Moyo')->assertSee('Replied');
    }

    public function test_reply_says_plainly_when_email_is_not_switched_on(): void
    {
        config(['mail.default' => 'log']);

        $this->actingAs($this->admin)->post('/admin/enquiries/' . $this->enquiry->id . '/reply', ['body' => 'Thank you for your message.'])
            ->assertSessionHas('status', 'Reply saved, but not emailed: email sending is not switched on for this site yet.');

        $this->assertSame('logged', $this->enquiry->messages()->firstOrFail()->delivery);
        $this->actingAs($this->admin)->get('/admin/enquiries/' . $this->enquiry->id)->assertOk()->assertSee('Not emailed: email is off');
    }

    public function test_customer_reply_can_be_recorded_and_puts_the_conversation_back_in_waiting(): void
    {
        $this->actingAs($this->admin)->post('/admin/enquiries/' . $this->enquiry->id . '/done')->assertRedirect();
        $this->assertFalse($this->enquiry->fresh()->awaiting_reply);

        $this->actingAs($this->admin)->post('/admin/enquiries/' . $this->enquiry->id . '/received', ['body' => 'Please send me the invoice.'])->assertRedirect();
        $this->assertTrue($this->enquiry->fresh()->awaiting_reply);
        $this->actingAs($this->admin)->get('/admin/enquiries/' . $this->enquiry->id)->assertOk()->assertSee('Please send me the invoice.');

        $this->actingAs($this->admin)->put('/admin/enquiries/' . $this->enquiry->id, ['status' => 'closed'])->assertRedirect();
        $this->assertFalse($this->enquiry->fresh()->awaiting_reply);
    }

    public function test_only_roles_with_enquiries_can_use_the_message_box(): void
    {
        $finance = User::factory()->create(['role' => 'finance']);
        $sales = User::factory()->create(['role' => 'sales']);

        $this->actingAs($finance)->get('/admin/messages')->assertForbidden();
        $this->actingAs($finance)->post('/admin/enquiries/' . $this->enquiry->id . '/reply', ['body' => 'No.'])->assertForbidden();
        $this->actingAs($sales)->get('/admin/messages')->assertOk();
    }
}
