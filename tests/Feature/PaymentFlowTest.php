<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Order;
use App\Models\Publication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentFlowTest extends TestCase
{
    use RefreshDatabase;

    private array $customer = ['customer_name' => 'Test Person', 'customer_email' => 'buyer@example.com', 'accept' => '1'];

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_enrolling_creates_an_order_and_goes_to_the_pay_page(): void
    {
        $course = Course::where('slug', 'iso-9001-quality-management')->firstOrFail();

        $this->get('/courses/' . $course->slug)->assertOk()->assertSee('/courses/' . $course->slug . '/enrol', false);
        $this->get('/courses/' . $course->slug . '/enrol')->assertOk()->assertSee('Continue to payment');

        $response = $this->post('/courses/' . $course->slug . '/enrol', $this->customer);

        $order = Order::firstOrFail();
        $response->assertRedirect('/pay/' . $order->token);
        $this->assertSame('enrolment', $order->type);
        $this->assertSame('pending', $order->status);
        $this->assertEquals(500, (float) $order->amount);
        $this->assertStringStartsWith('AFR-', $order->reference);

        $this->get('/pay/' . $order->token)->assertOk()->assertSee($order->reference)->assertSee('Online payment is being set up');
    }

    public function test_terms_must_be_accepted(): void
    {
        $this->post('/courses/iso-9001-quality-management/enrol', ['customer_name' => 'Test Person', 'customer_email' => 'buyer@example.com'])
            ->assertSessionHasErrors('accept');

        $this->assertSame(0, Order::count());
    }

    public function test_buying_a_publication_creates_an_order(): void
    {
        $publication = Publication::where('type', 'book')->firstOrFail();

        $this->get('/publications')->assertOk()->assertSee('/publications/' . $publication->id . '/buy', false);
        $this->get('/publications/' . $publication->id . '/buy')->assertOk();
        $this->post('/publications/' . $publication->id . '/buy', $this->customer)->assertRedirect();

        $order = Order::firstOrFail();
        $this->assertSame('publication', $order->type);
        $this->assertEquals(40, (float) $order->amount);
    }

    public function test_test_gateway_takes_payment_on_uat(): void
    {
        config(['afroiso.uat' => true, 'afroiso.uat_password' => null, 'afroiso.payment_gateway' => 'test']);
        $this->post('/courses/iso-9001-quality-management/enrol', $this->customer);
        $order = Order::firstOrFail();

        $this->get('/pay/' . $order->token)->assertOk()->assertSee('Test payment');
        $this->post('/pay/' . $order->token, ['outcome' => 'fail'])->assertRedirect('/pay/' . $order->token);
        $this->assertSame('failed', $order->fresh()->status);

        $this->post('/pay/' . $order->token, ['outcome' => 'success'])->assertRedirect('/pay/' . $order->token);
        $this->assertSame('paid', $order->fresh()->status);
        $this->assertNotNull($order->fresh()->paid_at);
        $this->get('/pay/' . $order->token)->assertOk()->assertSee('Your payment was received');
    }

    public function test_test_gateway_is_ignored_on_the_live_site(): void
    {
        config(['afroiso.payment_gateway' => 'test']);
        $this->post('/courses/iso-9001-quality-management/enrol', $this->customer);
        $order = Order::firstOrFail();

        $this->get('/pay/' . $order->token)->assertOk()->assertDontSee('Test payment');
        $this->post('/pay/' . $order->token, ['outcome' => 'success']);
        $this->assertSame('pending', $order->fresh()->status);
    }

    public function test_admin_creates_a_consulting_invoice_with_a_pay_link(): void
    {
        $this->get('/admin/orders')->assertRedirect('/admin/login');

        $admin = User::factory()->create();
        $this->actingAs($admin)->get('/admin/orders')->assertOk();
        $this->actingAs($admin)->get('/admin/orders/create')->assertOk();

        $this->actingAs($admin)->post('/admin/orders', [
            'customer_name' => 'Client Contact',
            'customer_email' => 'client@example.com',
            'organisation' => 'Example Mining',
            'description' => 'ISO 9001 gap analysis',
            'amount' => '1250.00',
        ])->assertRedirect();

        $order = Order::firstOrFail();
        $this->assertSame('invoice', $order->type);
        $this->actingAs($admin)->get('/admin/orders/' . $order->id)->assertOk()->assertSee('/pay/' . $order->token, false);

        auth()->logout();
        $this->get('/pay/' . $order->token)->assertOk()->assertSee('ISO 9001 gap analysis')->assertSee('US$1,250.00');

        $this->actingAs($admin)->put('/admin/orders/' . $order->id, ['action' => 'mark-paid'])->assertRedirect();
        $this->assertSame('paid', $order->fresh()->status);
    }

    public function test_unknown_pay_link_is_not_found(): void
    {
        $this->get('/pay/not-a-real-token')->assertNotFound();
    }
}
