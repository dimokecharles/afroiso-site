<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\SimpleText;
use App\Support\SiteText;
use Illuminate\Http\Request;

class TextController extends Controller
{
    public function index()
    {
        $groups = collect(config('texts.items'))
            ->map(fn (array $item, string $key) => $item + ['key' => $key, 'changed' => SiteText::custom($key) !== null])
            ->groupBy('group');

        return view('admin.texts.index', [
            'groups' => $groups,
            'policiesPublished' => $this->policiesPublished(),
            'lockedByEnv' => (bool) config('afroiso.policies_published'),
        ]);
    }

    public function edit(string $key)
    {
        $item = $this->item($key);
        $value = SiteText::custom($key);

        // A policy that has never been edited starts from its built-in draft, so nobody has to retype it.
        if ($value === null) {
            $value = str_starts_with($key, 'policy.')
                ? SimpleText::fromHtml(view('pages.policies.' . substr($key, 7))->render())
                : $item['default'];
        }

        return view('admin.texts.form', ['key' => $key, 'item' => $item, 'value' => $value, 'changed' => SiteText::custom($key) !== null]);
    }

    public function update(Request $request, string $key)
    {
        $item = $this->item($key);

        if ($request->input('action') === 'reset') {
            SiteText::set($key, null);

            return redirect()->route('admin.texts.index')->with('status', $item['label'] . ' is back to its built-in wording.');
        }

        $data = $request->validate(['value' => ['required', 'string', $item['kind'] === 'line' ? 'max:200' : 'max:60000']]);
        $value = $item['kind'] === 'line' ? trim(preg_replace('/\s+/', ' ', $data['value'])) : $data['value'];
        SiteText::set($key, $value);

        return redirect()->route('admin.texts.index')->with('status', $item['label'] . ' saved.');
    }

    /** Show or hide the policy pages on the live website. */
    public function publish(Request $request)
    {
        SiteText::set('policies.published', $request->boolean('published') ? '1' : null);

        return back()->with('status', $request->boolean('published') ? 'The policies are now published.' : 'The policies are no longer published.');
    }

    private function item(string $key): array
    {
        $item = config('texts.items.' . $key);
        abort_if($item === null, 404);

        return $item;
    }

    private function policiesPublished(): bool
    {
        return config('afroiso.policies_published') || SiteText::custom('policies.published') === '1';
    }
}
