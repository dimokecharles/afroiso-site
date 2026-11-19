<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const BOOK_PLACEHOLDER = '[BOOK TITLE]';

    private const MAGAZINE_PLACEHOLDER = '[MAGAZINE TITLE]';

    /**
     * @var array<string, array{type: string, title: string, description: string, price: float, cover_path: string, sort_order: int}>
     */
    private array $publications = [
        'book' => [
            'type' => 'book',
            'title' => 'Systems Over Hustle: ISO Standards for African SMEs',
            'description' => "By Norman Sifiso Ndlovu.\nHow African small and medium-sized enterprises can use ISO standards to build systems that run the business, instead of relying on hustle.",
            'price' => 40,
            'cover_path' => 'images/publications/systems-over-hustle.jpg',
            'sort_order' => 10,
        ],
        'november' => [
            'type' => 'magazine',
            'title' => 'StandardIQ, 1–15 November 2026: The Quality Leadership Test',
            'description' => "AfroISO's monthly ISO magazine.\nIn this issue: South Africa adopts ISO 9001:2026, and Blake Mosley-Lefatola, CEO of SABS, on turning standards into better performance.",
            'price' => 4.99,
            'cover_path' => 'images/publications/standardiq-november-2026.webp',
            'sort_order' => 20,
        ],
        'october' => [
            'type' => 'magazine',
            'title' => 'StandardIQ, 5 October – 4 November 2026: ISO 9001:2026, the New Quality Era Begins',
            'description' => "AfroISO's monthly ISO magazine.\nIn this issue: the new edition of ISO 9001, featuring Khaled Soufi (ISO President), Francisca Tatchouop Belobe (AU Commissioner for ETTIM) and Hermogène Nsengimana (Secretary General, ARSO).",
            'price' => 4.99,
            'cover_path' => 'images/publications/standardiq-october-2026.webp',
            'sort_order' => 30,
        ],
    ];

    /**
     * Replace the starter placeholders with the real publications.
     * Rows already edited in the admin panel are left alone.
     */
    public function up(): void
    {
        $now = now();

        DB::table('publications')->where('title', self::BOOK_PLACEHOLDER)
            ->update($this->publications['book'] + ['updated_at' => $now]);

        DB::table('publications')->where('title', self::MAGAZINE_PLACEHOLDER)
            ->update($this->publications['october'] + ['updated_at' => $now]);

        // Only add the November issue on sites that already have publications (fresh installs get it from the seeder).
        $hasPublications = DB::table('publications')->exists();
        $hasNovember = DB::table('publications')->where('title', $this->publications['november']['title'])->exists();

        if ($hasPublications && ! $hasNovember) {
            DB::table('publications')->insert($this->publications['november'] + [
                'is_published' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        DB::table('publications')->where('title', $this->publications['november']['title'])->delete();

        DB::table('publications')->where('title', $this->publications['book']['title'])->update([
            'title' => self::BOOK_PLACEHOLDER,
            'description' => '[Short description of the book and its author]',
            'cover_path' => null,
        ]);

        DB::table('publications')->where('title', $this->publications['october']['title'])->update([
            'title' => self::MAGAZINE_PLACEHOLDER,
            'description' => '[Short description of the magazine and how often it is published]',
            'cover_path' => null,
        ]);
    }
};
