<?php

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\Order;
use App\Models\User;
use App\Support\SimpleText;
use App\Support\SiteText;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PhaseOneExtrasTest extends TestCase
{
    use RefreshDatabase;

    private function staff(string $role = 'admin'): User
    {
        return User::factory()->create(['role' => $role]);
    }

    private function paidOrder(): Order
    {
        $order = Order::create(['type' => 'invoice', 'description' => 'Gap analysis', 'customer_name' => 'Rudo Moyo', 'customer_email' => 'rudo@example.com', 'amount' => 400]);
        $order->markPaid('manual');

        return $order->fresh();
    }

    // Certificate QR codes

    public function test_certificate_qr_code_is_an_svg_and_can_be_downloaded(): void
    {
        $certificate = Certificate::create(['number' => 'AFR/2026/0042', 'holder_name' => 'Tendai Ncube', 'course_title' => 'Internal Auditor', 'issued_on' => '2026-11-01', 'status' => 'valid']);
        $admin = $this->staff();

        $response = $this->actingAs($admin)->get('/admin/certificates/' . $certificate->id . '/qr')->assertOk();
        $this->assertStringContainsString('image/svg+xml', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('<svg', $response->getContent());

        $download = $this->actingAs($admin)->get('/admin/certificates/' . $certificate->id . '/qr?download=1')->assertOk();
        $this->assertStringContainsString('certificate-AFR-2026-0042-qr.svg', $download->headers->get('Content-Disposition'));

        $this->actingAs($admin)->get('/admin/certificates/' . $certificate->id . '/edit')->assertOk()->assertSee('Download QR code');
        $this->actingAs($admin)->get('/admin/certificates')->assertOk()->assertSee('QR code');
    }

    public function test_qr_code_needs_the_content_area(): void
    {
        $certificate = Certificate::create(['number' => 'C-1', 'holder_name' => 'A', 'course_title' => 'B', 'issued_on' => '2026-11-01', 'status' => 'valid']);

        $this->get('/admin/certificates/' . $certificate->id . '/qr')->assertRedirect('/admin/login');
        $this->actingAs($this->staff('finance'))->get('/admin/certificates/' . $certificate->id . '/qr')->assertStatus(403);
    }

    // Refunded status

    public function test_paid_order_can_be_marked_refunded_and_leaves_the_paid_total(): void
    {
        $order = $this->paidOrder();
        $admin = $this->staff('finance');

        $this->actingAs($admin)->get('/admin/orders/' . $order->id)->assertOk()->assertSee('Mark as refunded');
        $this->actingAs($admin)->put('/admin/orders/' . $order->id, ['action' => 'refund'])->assertRedirect();

        $order->refresh();
        $this->assertSame('refunded', $order->status);
        $this->assertNotNull($order->refunded_at);
        $this->assertFalse($order->canBePaid());
        $this->assertSame(0.0, (float) Order::where('status', 'paid')->sum('amount'));

        $this->actingAs($admin)->get('/admin/orders/' . $order->id)->assertOk()->assertSee('Refunded')->assertDontSee('Mark as refunded');
        $this->actingAs($admin)->get('/admin/orders?status=refunded')->assertOk()->assertSee($order->reference);
        $this->get('/pay/' . $order->token)->assertOk()->assertSee('This payment was refunded');
        $this->actingAs($this->staff())->get('/admin')->assertOk();
    }

    public function test_unpaid_order_cannot_be_marked_refunded(): void
    {
        $order = Order::create(['type' => 'invoice', 'description' => 'Audit', 'customer_name' => 'Rudo', 'customer_email' => 'rudo@example.com', 'amount' => 100]);

        $this->actingAs($this->staff())->put('/admin/orders/' . $order->id, ['action' => 'refund']);

        $this->assertSame('pending', $order->fresh()->status);
    }

    // Website text

    public function test_built_in_wording_shows_until_it_is_edited(): void
    {
        $this->seed();
        $this->get('/')->assertOk()->assertSee('Training the people who build and audit management systems.');
        $this->get('/about')->assertOk()->assertSee('most trusted institute');
    }

    public function test_staff_can_edit_page_text_and_go_back_to_the_built_in_wording(): void
    {
        $this->seed();
        $editor = $this->staff('content');

        $this->actingAs($editor)->get('/admin/texts')->assertOk()->assertSee('Home page: main heading')->assertSee('Privacy policy');
        $this->actingAs($editor)->get('/admin/texts/home.hero_title')->assertOk()->assertSee('Training the people who build and audit management systems.');
        $this->actingAs($editor)->put('/admin/texts/home.hero_title', ['action' => 'save', 'value' => 'ISO training <b>for</b> Africa'])->assertRedirect('/admin/texts');
        $this->actingAs($editor)->put('/admin/texts/about.mission', ['action' => 'save', 'value' => 'A new mission.'])->assertRedirect('/admin/texts');

        $this->get('/')->assertOk()->assertSee('ISO training <b>for</b> Africa')->assertDontSee('ISO training <b>for</b> Africa', false);
        $this->get('/about')->assertOk()->assertSee('A new mission.');

        $this->actingAs($editor)->put('/admin/texts/home.hero_title', ['action' => 'reset'])->assertRedirect('/admin/texts');
        $this->get('/')->assertOk()->assertSee('Training the people who build and audit management systems.');
    }

    public function test_unknown_text_and_other_roles_are_refused(): void
    {
        $this->actingAs($this->staff())->get('/admin/texts/not.a.text')->assertNotFound();
        $this->actingAs($this->staff('sales'))->get('/admin/texts')->assertStatus(403);
        $this->actingAs($this->staff('sales'))->put('/admin/texts/home.hero_title', ['action' => 'save', 'value' => 'x'])->assertStatus(403);
        $this->assertNull(SiteText::custom('home.hero_title'));
    }

    public function test_policy_editor_starts_from_the_built_in_draft(): void
    {
        $this->actingAs($this->staff())->get('/admin/texts/policy.refund-policy')
            ->assertOk()
            ->assertSee('How to ask for a refund')
            ->assertSee('## ', false);
    }

    public function test_edited_policy_replaces_the_draft_and_can_be_published_from_the_admin(): void
    {
        $admin = $this->staff();
        $text = "## Our promise\n\nWe refund within 14 days.\n\n- Ask by email\n- Quote your reference";

        $this->actingAs($admin)->put('/admin/texts/policy.refund-policy', ['action' => 'save', 'value' => $text])->assertRedirect('/admin/texts');

        // Not published yet: the live site still shows the placeholder.
        $this->get('/refund-policy')->assertOk()->assertSee('being finalised')->assertDontSee('Our promise');

        $this->actingAs($admin)->post('/admin/texts/publish', ['published' => 1])->assertRedirect();
        $this->get('/refund-policy')
            ->assertOk()
            ->assertSee('<h2>Our promise</h2>', false)
            ->assertSee('<li>Ask by email</li>', false)
            ->assertDontSee('How to ask for a refund')
            ->assertDontSee('Draft for review')
            ->assertSee('content="index, follow"', false);
        // A policy that was not edited shows its built-in text once published.
        $this->get('/terms')->assertOk()->assertSee('Enrolment and payment');

        $this->actingAs($admin)->post('/admin/texts/publish', ['published' => 0])->assertRedirect();
        $this->get('/refund-policy')->assertOk()->assertSee('being finalised');
    }

    public function test_simple_text_formats_headings_and_lists_and_escapes_html(): void
    {
        $html = SimpleText::toHtml("Intro\n\n## Head\n### Sub\n- a\n- b\n1. one\n2. two\n\n<script>x</script>");

        $this->assertSame('<p>Intro</p><h2>Head</h2><h3>Sub</h3><ul><li>a</li><li>b</li></ul><ol><li>one</li><li>two</li></ol><p>&lt;script&gt;x&lt;/script&gt;</p>', $html);
        $this->assertSame("Hello & you.\n\n## Title\n\n- one\n- two", SimpleText::fromHtml('<p>Hello &amp; <strong>you</strong>.</p><h2>Title</h2><ul><li>one</li><li>two</li></ul>'));
    }
}
