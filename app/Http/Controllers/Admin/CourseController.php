<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CourseController extends Controller
{
    public function index()
    {
        return view('admin.courses.index', ['courses' => Course::orderBy('sort_order')->orderBy('title')->get()]);
    }

    public function create()
    {
        return view('admin.courses.form', ['course' => new Course(['delivery' => 'E-learning', 'is_published' => true])]);
    }

    public function store(Request $request)
    {
        Course::create($this->validated($request));

        return redirect()->route('admin.courses.index')->with('status', 'Course added.');
    }

    public function edit(Course $course)
    {
        return view('admin.courses.form', ['course' => $course]);
    }

    public function update(Request $request, Course $course)
    {
        $course->update($this->validated($request, $course));

        return redirect()->route('admin.courses.index')->with('status', 'Course saved.');
    }

    public function destroy(Course $course)
    {
        $course->delete();

        return redirect()->route('admin.courses.index')->with('status', 'Course deleted.');
    }

    private function validated(Request $request, ?Course $course = null): array
    {
        $request->merge(['slug' => Str::slug($request->input('slug') ?: $request->input('title', ''))]);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('courses', 'slug')->ignore($course?->id)],
            'code' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'summary' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'fee' => ['nullable', 'numeric', 'min:0'],
            'delivery' => ['required', 'string', 'max:255'],
            'start_date' => ['nullable', 'date'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $data['is_published'] = $request->boolean('is_published');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        return $data;
    }
}
