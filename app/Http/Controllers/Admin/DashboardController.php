<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Course;
use App\Models\Enquiry;
use App\Models\Order;
use App\Models\PageView;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function __invoke(\Illuminate\Http\Request $request)
    {
        if (! $request->user()->canAccess('dashboard')) {
            return $request->user()->canAccess('content') ? redirect()->route('admin.courses.index') : redirect()->route('admin.account');
        }

        $now = now();
        $thisMonth = $now->copy()->startOfMonth();
        $lastMonth = $thisMonth->copy()->subMonth();

        // Paid orders from the last twelve months drive every money figure on the page.
        $paid = Order::where('status', 'paid')->where('paid_at', '>=', $thisMonth->copy()->subMonths(11))->get();
        $paidThisMonth = $paid->filter(fn (Order $order) => $order->paid_at >= $thisMonth);
        $paidLastMonth = $paid->filter(fn (Order $order) => $order->paid_at >= $lastMonth && $order->paid_at < $thisMonth);

        $months = collect(range(5, 0))->map(function (int $back) use ($thisMonth, $paid) {
            $start = $thisMonth->copy()->subMonths($back);
            $end = $start->copy()->addMonth();

            return [
                'label' => $start->format('M'),
                'full' => $start->format('F Y'),
                'total' => (float) $paid->filter(fn (Order $order) => $order->paid_at >= $start && $order->paid_at < $end)->sum('amount'),
            ];
        });

        $pending = Order::where('status', 'pending')->get(['amount']);
        $statusCounts = Order::selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');

        // Conversion: of the people who sent an enquiry, how many went on to pay for something.
        $enquirers = Enquiry::whereNotNull('contact_id')->distinct()->pluck('contact_id');
        $converted = $enquirers->isEmpty() ? 0 : Order::where('status', 'paid')->whereIn('contact_id', $enquirers)->distinct()->count('contact_id');

        $since = $now->copy()->subDays(13)->startOfDay();
        $views = PageView::where('viewed_on', '>=', $since->toDateString())->get(['path', 'referrer_host', 'visitor', 'viewed_on']);
        $days = collect(range(13, 0))->map(function (int $back) use ($now, $views) {
            $day = $now->copy()->subDays($back);
            $onDay = $views->filter(fn (PageView $view) => $view->viewed_on->isSameDay($day));

            return ['label' => $day->format('j M'), 'short' => $day->format('j'), 'visitors' => $onDay->unique('visitor')->count(), 'views' => $onDay->count()];
        });

        $courses = Course::orderBy('sort_order')->get();
        $enquirySubjects = Enquiry::whereNotNull('subject')->pluck('subject');

        return view('admin.dashboard', [
            'revenueThisMonth' => (float) $paidThisMonth->sum('amount'),
            'revenueLastMonth' => (float) $paidLastMonth->sum('amount'),
            'paidCountThisMonth' => $paidThisMonth->count(),
            'pendingCount' => $pending->count(),
            'pendingValue' => (float) $pending->sum('amount'),
            'newEnquiries' => Enquiry::where('status', 'new')->count(),
            'openLeads' => Enquiry::whereNotIn('status', ['converted', 'closed'])->count(),
            'contactCount' => Contact::count(),
            'enquirerCount' => $enquirers->count(),
            'convertedCount' => $converted,
            'months' => $months,
            'monthMax' => max(1, $months->max('total')),
            'statusCounts' => collect(Order::STATUSES)->map(fn ($label, $key) => ['label' => $label, 'total' => (int) ($statusCounts[$key] ?? 0)]),
            'topCourses' => $this->top($paid->where('type', 'enrolment'), 'course_id', $courses->pluck('title', 'id')),
            'topPublications' => $this->top($paid->where('type', 'publication'), 'publication_id', null),
            'courseInterest' => $courses->map(fn (Course $course) => [
                'title' => $course->title,
                'enquiries' => $enquirySubjects->filter(fn ($subject) => str_contains($subject, $course->title))->count(),
                'views' => $views->where('path', '/courses/' . $course->slug)->count(),
            ])->sortByDesc(fn ($row) => $row['enquiries'] * 1000 + $row['views'])->values(),
            'days' => $days,
            'dayMax' => max(1, $days->max('visitors')),
            'visitorTotal' => $views->unique(fn (PageView $view) => $view->visitor . $view->viewed_on->toDateString())->count(),
            'viewTotal' => $views->count(),
            'topReferrers' => $views->whereNotNull('referrer_host')->countBy('referrer_host')->sortDesc()->take(5),
            'topPages' => $views->countBy('path')->sortDesc()->take(6),
            'enquiries' => Enquiry::latest()->limit(6)->get(),
            'orders' => Order::latest()->limit(6)->get(),
            'followUps' => Enquiry::whereNotIn('status', ['converted', 'closed'])->whereNotNull('follow_up_on')->where('follow_up_on', '<=', Carbon::today())->orderBy('follow_up_on')->limit(6)->get(),
        ]);
    }

    /** Best sellers: count and revenue per course or publication, biggest first. */
    private function top($orders, string $key, $titles)
    {
        return $orders->groupBy(fn (Order $order) => $order->{$key} ?: $order->description)
            ->map(fn ($group, $id) => [
                'title' => ($titles[$id] ?? null) ?: $group->first()->description,
                'count' => $group->count(),
                'revenue' => (float) $group->sum('amount'),
            ])
            ->sortByDesc('revenue')
            ->take(6)
            ->values();
    }
}
