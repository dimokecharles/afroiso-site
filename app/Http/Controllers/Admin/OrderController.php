<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\OrderMessage;
use App\Models\Order;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $type = array_key_exists((string) $request->query('type'), Order::TYPES) ? $request->query('type') : null;
        $status = array_key_exists((string) $request->query('status'), Order::STATUSES) ? $request->query('status') : null;

        $orders = Order::query()
            ->when($type, fn ($query) => $query->where('type', $type))
            ->when($status, fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(30)
            ->withQueryString();

        return view('admin.orders.index', [
            'orders' => $orders,
            'type' => $type,
            'status' => $status,
            'paidTotal' => Order::where('status', 'paid')->sum('amount'),
            'pendingCount' => Order::where('status', 'pending')->count(),
        ]);
    }

    /** Form for a new consulting invoice. */
    public function create(Request $request)
    {
        // Opened from a project: the client's details are filled in and the invoice is linked to the project.
        return view('admin.orders.form', ['project' => Project::with('contact')->find((int) $request->query('project'))]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:120'],
            'customer_email' => ['required', 'email', 'max:160'],
            'customer_phone' => ['nullable', 'string', 'max:60'],
            'organisation' => ['nullable', 'string', 'max:160'],
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:99999999'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'project_id' => ['nullable', 'exists:projects,id'],
        ]);

        $order = Order::create($data + ['type' => 'invoice']);

        return redirect()->route('admin.orders.show', $order)->with('status', 'Invoice created. Copy the pay link below, or email it to the client.');
    }

    public function show(Order $order)
    {
        return view('admin.orders.show', ['order' => $order]);
    }

    /** Email the pay link to the client. */
    public function send(Order $order)
    {
        abort_unless($order->canBePaid(), 422);

        try {
            Mail::to($order->customer_email)->send(new OrderMessage($order, 'paylink'));
        } catch (\Throwable $e) {
            Log::warning('Pay link email could not be sent: ' . $e->getMessage());

            return back()->withErrors('The email could not be sent. Copy the pay link and send it yourself.');
        }

        return back()->with('status', 'Pay link emailed to ' . $order->customer_email . '.');
    }

    /** Record a payment received outside the website, cancel an order, or record a refund. */
    public function update(Request $request, Order $order)
    {
        $action = $request->validate(['action' => ['required', 'in:mark-paid,cancel,refund']])['action'];

        if ($action === 'mark-paid' && $order->canBePaid()) {
            $order->markPaid('manual', $request->user()->email);

            return back()->with('status', 'Marked as paid.');
        }

        if ($action === 'cancel' && $order->canBePaid()) {
            $order->update(['status' => 'cancelled']);

            return back()->with('status', 'Order cancelled.');
        }

        if ($action === 'refund' && $order->isPaid()) {
            $order->markRefunded();

            return back()->with('status', 'Marked as refunded. This only records the refund: the money itself must be returned through the bank or payment provider.');
        }

        return back();
    }
}
