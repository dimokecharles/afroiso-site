<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StaffController extends Controller
{
    public function index()
    {
        return view('admin.staff.index', ['staff' => User::orderBy('name')->get()]);
    }

    public function create()
    {
        return view('admin.staff.form', ['person' => new User(['role' => 'sales', 'is_active' => true])]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        User::create($data);

        return redirect()->route('admin.staff.index')->with('status', 'Login created for ' . $data['name'] . '. Give them their email address and the password you set.');
    }

    public function edit(User $staff)
    {
        return view('admin.staff.form', ['person' => $staff]);
    }

    public function update(Request $request, User $staff)
    {
        $data = $this->validated($request, $staff);

        // The panel must always keep at least one working administrator, and nobody can lock themselves out.
        $losesAdmin = $staff->role === 'admin' && $staff->is_active && ($data['role'] !== 'admin' || ! $data['is_active']);
        if ($losesAdmin && $staff->is($request->user())) {
            return back()->withErrors('You cannot remove your own administrator access. Ask another administrator to do it.')->withInput();
        }
        if ($losesAdmin && User::where('role', 'admin')->where('is_active', true)->count() <= 1) {
            return back()->withErrors('This is the only active administrator. Make someone else an administrator first.')->withInput();
        }

        if (empty($data['password'])) {
            unset($data['password']);
        }
        $staff->update($data);

        return redirect()->route('admin.staff.index')->with('status', 'Saved.');
    }

    private function validated(Request $request, ?User $person = null): array
    {
        $request->merge(['email' => strtolower(trim((string) $request->input('email'))), 'is_active' => $request->boolean('is_active')]);

        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:160', Rule::unique('users', 'email')->ignore($person?->id)],
            'role' => ['required', Rule::in(array_keys(User::ROLES))],
            'is_active' => ['boolean'],
            'password' => [$person ? 'nullable' : 'required', 'string', Password::min(10)],
        ]);
    }
}
