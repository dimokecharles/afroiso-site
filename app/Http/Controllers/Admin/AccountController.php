<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class AccountController extends Controller
{
    /** Every staff member can see their own details and change their own password. */
    public function edit(Request $request)
    {
        return view('admin.account', ['person' => $request->user()]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', Password::min(10), 'confirmed'],
        ], ['current_password.current_password' => 'The current password is not correct.']);

        $request->user()->update(['password' => $data['password']]);

        return back()->with('status', 'Your password has been changed.');
    }
}
