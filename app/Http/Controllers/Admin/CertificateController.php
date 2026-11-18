<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CertificateController extends Controller
{
    public function index()
    {
        return view('admin.certificates.index', ['certificates' => Certificate::latest('issued_on')->paginate(30)]);
    }

    public function create()
    {
        return view('admin.certificates.form', ['certificate' => new Certificate(['status' => 'valid'])]);
    }

    public function store(Request $request)
    {
        Certificate::create($this->validated($request));

        return redirect()->route('admin.certificates.index')->with('status', 'Certificate added.');
    }

    public function edit(Certificate $certificate)
    {
        return view('admin.certificates.form', ['certificate' => $certificate]);
    }

    public function update(Request $request, Certificate $certificate)
    {
        $certificate->update($this->validated($request, $certificate));

        return redirect()->route('admin.certificates.index')->with('status', 'Certificate saved.');
    }

    public function destroy(Certificate $certificate)
    {
        $certificate->delete();

        return redirect()->route('admin.certificates.index')->with('status', 'Certificate deleted.');
    }

    private function validated(Request $request, ?Certificate $certificate = null): array
    {
        $request->merge(['number' => trim((string) $request->input('number'))]);

        return $request->validate([
            'number' => ['required', 'string', 'max:80', Rule::unique('certificates', 'number')->ignore($certificate?->id)],
            'holder_name' => ['required', 'string', 'max:255'],
            'course_title' => ['required', 'string', 'max:255'],
            'issued_on' => ['required', 'date'],
            'status' => ['required', 'in:valid,revoked'],
        ]);
    }
}
