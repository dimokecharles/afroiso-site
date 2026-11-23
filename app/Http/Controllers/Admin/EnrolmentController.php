<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Order;
use Illuminate\Http\Request;

class EnrolmentController extends Controller
{
    /** Who has enrolled on which course. An enrolment is an order of type "enrolment". */
    public function index(Request $request)
    {
        $courses = Course::orderBy('sort_order')->get();
        $courseId = $courses->firstWhere('id', (int) $request->query('course'))?->id;
        $status = array_key_exists((string) $request->query('status'), Order::STATUSES) ? $request->query('status') : null;

        $enrolments = Order::with('course')
            ->where('type', 'enrolment')
            ->when($courseId, fn ($query) => $query->where('course_id', $courseId))
            ->when($status, fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(30)
            ->withQueryString();

        $totals = Order::where('type', 'enrolment')->selectRaw('course_id, status, count(*) as total')->groupBy('course_id', 'status')->get();

        return view('admin.enrolments.index', [
            'enrolments' => $enrolments,
            'courses' => $courses,
            'courseId' => $courseId,
            'status' => $status,
            'summary' => $courses->map(fn (Course $course) => [
                'course' => $course,
                'paid' => (int) $totals->where('course_id', $course->id)->where('status', 'paid')->sum('total'),
                'pending' => (int) $totals->where('course_id', $course->id)->where('status', 'pending')->sum('total'),
            ]),
        ]);
    }
}
