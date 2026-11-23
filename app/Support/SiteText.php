<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

/**
 * Website text that staff can change in the admin panel. The editable pieces and their built-in wording
 * are listed in config/texts.php. A piece that has not been changed shows its built-in wording.
 */
class SiteText
{
    /** The saved wording is read once per request and kept in the app container. */
    private const CACHE = 'afroiso.site_texts';

    public static function get(string $key): string
    {
        return self::saved()[$key] ?? (string) config('texts.items.' . $key . '.default', '');
    }

    /** The saved wording only, or null when the built-in wording is still in use. */
    public static function custom(string $key): ?string
    {
        return self::saved()[$key] ?? null;
    }

    public static function updatedAt(string $key): ?\Illuminate\Support\Carbon
    {
        $value = DB::table('site_texts')->where('key', $key)->value('updated_at');

        return $value ? \Illuminate\Support\Carbon::parse($value) : null;
    }

    public static function html(string $key): string
    {
        return SimpleText::toHtml(self::get($key));
    }

    public static function set(string $key, ?string $value): void
    {
        if ($value === null || trim($value) === '') {
            DB::table('site_texts')->where('key', $key)->delete();
        } else {
            DB::table('site_texts')->updateOrInsert(['key' => $key], ['value' => $value, 'updated_at' => now()]);
        }

        app()->forgetInstance(self::CACHE);
    }

    private static function saved(): array
    {
        if (! app()->bound(self::CACHE)) {
            try {
                $saved = DB::table('site_texts')->pluck('value', 'key')->all();
            } catch (\Throwable $e) {
                // Before the table exists (a first deploy), every piece shows its built-in wording.
                $saved = [];
            }
            app()->instance(self::CACHE, $saved);
        }

        return app(self::CACHE);
    }
}
