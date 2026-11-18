<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Publication;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PublicationController extends Controller
{
    public function index()
    {
        return view('admin.publications.index', ['publications' => Publication::orderBy('sort_order')->orderBy('title')->get()]);
    }

    public function create()
    {
        return view('admin.publications.form', ['publication' => new Publication(['type' => 'book', 'is_published' => true])]);
    }

    public function store(Request $request)
    {
        Publication::create($this->validated($request));

        return redirect()->route('admin.publications.index')->with('status', 'Publication added.');
    }

    public function edit(Publication $publication)
    {
        return view('admin.publications.form', ['publication' => $publication]);
    }

    public function update(Request $request, Publication $publication)
    {
        $publication->update($this->validated($request, $publication));

        return redirect()->route('admin.publications.index')->with('status', 'Publication saved.');
    }

    public function destroy(Publication $publication)
    {
        $this->removeCover($publication->cover_path);
        $publication->delete();

        return redirect()->route('admin.publications.index')->with('status', 'Publication deleted.');
    }

    private function validated(Request $request, ?Publication $publication = null): array
    {
        $data = $request->validate([
            'type' => ['required', 'in:book,magazine'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'cover' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        unset($data['cover']);
        $data['is_published'] = $request->boolean('is_published');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        // Covers are saved straight into public/uploads so no storage link is needed on shared hosting.
        if ($request->hasFile('cover')) {
            $file = $request->file('cover');
            $name = Str::random(24) . '.' . $file->extension();
            $file->move(public_path('uploads'), $name);
            $this->removeCover($publication?->cover_path);
            $data['cover_path'] = 'uploads/' . $name;
        }

        return $data;
    }

    private function removeCover(?string $path): void
    {
        if ($path && str_starts_with($path, 'uploads/') && is_file(public_path($path))) {
            @unlink(public_path($path));
        }
    }
}
