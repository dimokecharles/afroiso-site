<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /** Two more StandardIQ issues. The sort order puts the newest issue first, after the book. */
    private array $issues = [
        [
            'type' => 'magazine',
            'title' => 'StandardIQ, 1–15 December 2026: Ready for 2027?',
            'description' => "AfroISO's monthly ISO magazine.\nIn this issue: a more inclusive future, with Norah Kimathi, co-founder of Zerobionic in Kenya, on robotics, learning and African ingenuity. Also: quality decisions for the year ahead.",
            'price' => 4.99,
            'cover_path' => 'images/publications/standardiq-december-2026.webp',
            'sort_order' => 12,
        ],
        [
            'type' => 'magazine',
            'title' => 'StandardIQ, 15–30 November 2026: The Price of Trust',
            'description' => "AfroISO's monthly ISO magazine.\nIn this issue: Simbarashe Mhuriro on building businesses that earn confidence. Also: African enterprise competing on quality, and what a quality mark really tells you.",
            'price' => 4.99,
            'cover_path' => 'images/publications/standardiq-november-15-30-2026.webp',
            'sort_order' => 16,
        ],
    ];

    public function up(): void
    {
        // Fresh installs get these from the seeder; this adds them to a site that already has publications.
        if (! DB::table('publications')->exists()) {
            return;
        }

        $now = now();
        foreach ($this->issues as $issue) {
            if (DB::table('publications')->where('title', $issue['title'])->doesntExist()) {
                DB::table('publications')->insert($issue + ['is_published' => true, 'created_at' => $now, 'updated_at' => $now]);
            }
        }
    }

    public function down(): void
    {
        foreach ($this->issues as $issue) {
            DB::table('publications')->where('title', $issue['title'])->delete();
        }
    }
};
