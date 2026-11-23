<?php

namespace App\Http\Controllers;

use App\Mail\OrderMessage;
use App\Models\Course;
use App\Models\Order;
use App\Models\Publication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Enrol on a course or buy a publication, then pay on the order's private pay page.
 * Consulting invoices are created in the admin panel and use the same pay page.
 */
class CheckoutController extends Controller
{
    public function enrol(Course $course)
    {
        abort_unless($course->is_published, 404);

        if ($course->fee === null) {
            return redirect()->route('contact', ['subject' => 'Enrol: ' . $course->title, 'interest' => 'A training course']);
        }

        return view('checkout.form', [
            'heading' => 'Enrol on this course',
            'eyebrow' => 'Enrolment',
            'itemTitle' => $course->title,
            'itemLines' => ['Delivery' => $course->delivery, 'Start date' => $course->startLabel(), 'Fee' => $course->feeLabel()],
            'action' => route('enrol.store', $course),
            'enquiryLink' => route('contact', ['subject' => 'Enrol: ' . $course->title, 'interest' => 'A training course']),
            'cover' => null,
        ]);
    }

    public function storeEnrolment(Request $request, Course $course)
    {
        abort_unless($course->is_published && $course->fee !== null, 404);

        return $this->placeOrder($request, [
            'type' => 'enrolment',
            'course_id' => $course->id,
            'description' => $course->title . ($course->start_date ? ' (starting ' . $course->startLabel() . ')' : ''),
            'amount' => $course->fee,
        ]);
    }

    public function buy(Publication $publication)
    {
        abort_unless($publication->is_published, 404);

        if ($publication->price === null) {
            return redirect()->route('contact', ['subject' => 'Order: ' . $publication->title, 'interest' => 'Ordering a book or magazine']);
        }

        return view('checkout.form', [
            'heading' => 'Buy this ' . strtolower($publication->typeLabel()),
            'eyebrow' => 'Checkout',
            'itemTitle' => $publication->title,
            'itemLines' => ['Type' => $publication->typeLabel(), 'Price' => $publication->priceLabel()],
            'action' => route('buy.store', $publication),
            'enquiryLink' => route('contact', ['subject' => 'Order: ' . $publication->title, 'interest' => 'Ordering a book or magazine']),
            'cover' => $publication->cover_path,
        ]);
    }

    public function storeOrder(Request $request, Publication $publication)
    {
        abort_unless($publication->is_published && $publication->price !== null, 404);

        return $this->placeOrder($request, [
            'type' => 'publication',
            'publication_id' => $publication->id,
            'description' => $publication->title,
            'amount' => $publication->price,
        ]);
    }

    public function show(Order $order)
    {
        return view('checkout.pay', ['order' => $order, 'gateway' => $this->gateway()]);
    }

    public function pay(Request $request, Order $order)
    {
        if (! $order->canBePaid()) {
            return redirect()->route('pay.show', ['order' => $order->token]);
        }

        // The test gateway only exists on the UAT site. It stands in for the real payment provider until that is connected.
        if ($this->gateway() === 'test') {
            if ($request->input('outcome') === 'fail') {
                $order->update(['status' => 'failed', 'gateway' => 'test']);

                return redirect()->route('pay.show', ['order' => $order->token])->with('payment', 'failed');
            }

            $order->markPaid('test', 'TEST-' . strtoupper(substr($order->token, 0, 10)));
            $this->send($order->customer_email, new OrderMessage($order, 'receipt'));

            return redirect()->route('pay.show', ['order' => $order->token])->with('payment', 'paid');
        }

        return redirect()->route('pay.show', ['order' => $order->token]);
    }

    private function placeOrder(Request $request, array $item)
    {
        // Spam bots fill in the hidden "website" field; real visitors never see it.
        if ($request->filled('website')) {
            return redirect()->route('home');
        }

        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:120'],
            'customer_email' => ['required', 'email', 'max:160'],
            'customer_phone' => ['nullable', 'string', 'max:60'],
            'organisation' => ['nullable', 'string', 'max:160'],
            'accept' => ['accepted'],
        ], ['accept.accepted' => 'Please confirm that you accept the terms and the refund policy.']);
        unset($data['accept']);

        $order = Order::create($data + $item);
        $this->send(config('afroiso.office_email'), new OrderMessage($order, 'placed'));

        return redirect()->route('pay.show', ['order' => $order->token]);
    }

    private function gateway(): string
    {
        $gateway = (string) config('afroiso.payment_gateway', 'none');

        return $gateway === 'test' && ! config('afroiso.uat') ? 'none' : $gateway;
    }

    private function send(string $to, OrderMessage $message): void
    {
        try {
            Mail::to($to)->send($message);
        } catch (\Throwable $e) {
            // The order is saved either way; only the email failed.
            Log::warning('Order email could not be sent: ' . $e->getMessage());
        }
    }
}
