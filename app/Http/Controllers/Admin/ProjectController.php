<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $stage = array_key_exists((string) $request->query('stage'), Project::STAGES) ? $request->query('stage') : null;

        $projects = Project::with(['contact', 'owner'])
            ->withSum(['invoices as paid_total' => fn ($query) => $query->where('status', 'paid')], 'amount')
            ->when($stage, fn ($query) => $query->where('stage', $stage))
            ->orderByRaw("case when stage in ('completed', 'on-hold') then 1 else 0 end")
            ->orderBy('target_date')
            ->paginate(30)
            ->withQueryString();

        return view('admin.projects.index', [
            'projects' => $projects,
            'stage' => $stage,
            'counts' => Project::selectRaw('stage, count(*) as total')->groupBy('stage')->pluck('total', 'stage'),
        ]);
    }

    public function create(Request $request)
    {
        return view('admin.projects.form', [
            'project' => new Project(['contact_id' => (int) $request->query('contact') ?: null]),
            'contacts' => Contact::orderBy('name')->get(['id', 'name', 'organisation']),
            'staff' => User::where('is_active', true)->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request)
    {
        $project = Project::create($this->validated($request));

        return redirect()->route('admin.projects.show', $project)->with('status', 'Project created.');
    }

    public function show(Project $project)
    {
        $project->load(['contact', 'owner', 'invoices', 'notes.user']);

        return view('admin.projects.show', [
            'project' => $project,
            'invoiced' => (float) $project->invoices->whereNotIn('status', ['cancelled'])->sum('amount'),
            'paid' => (float) $project->invoices->where('status', 'paid')->sum('amount'),
        ]);
    }

    public function edit(Project $project)
    {
        return view('admin.projects.form', [
            'project' => $project,
            'contacts' => Contact::orderBy('name')->get(['id', 'name', 'organisation']),
            'staff' => User::where('is_active', true)->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(Request $request, Project $project)
    {
        $project->update($this->validated($request));

        return redirect()->route('admin.projects.show', $project)->with('status', 'Project saved.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'contact_id' => ['required', 'exists:contacts,id'],
            'title' => ['required', 'string', 'max:200'],
            'standard' => ['nullable', 'string', 'max:80'],
            'stage' => ['required', Rule::in(array_keys(Project::STAGES))],
            'value' => ['nullable', 'numeric', 'min:0', 'max:9999999999'],
            'start_date' => ['nullable', 'date'],
            'target_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'description' => ['nullable', 'string', 'max:10000'],
        ]);
    }
}
