<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Publication;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin account. Set ADMIN_EMAIL and ADMIN_PASSWORD in .env before seeding.
        $password = config('afroiso.admin_password');
        if ($password) {
            User::updateOrCreate(
                ['email' => config('afroiso.admin_email')],
                ['name' => 'Administrator', 'password' => Hash::make($password)]
            );
        } else {
            $this->command?->warn('ADMIN_PASSWORD is empty in .env, so no admin account was created.');
        }

        $start = '2027-01-15';
        $courses = [
            ['ISO 9001:2026 Quality Management', 'ISO 9001:2026', 'Quality management', 'How an organisation consistently delivers what its customers and regulators require.', 500],
            ['ISO 14001:2026 Environmental Management', 'ISO 14001:2026', 'Environmental management', 'How an organisation controls its effect on the environment and meets its legal duties.', 500],
            ['ISO 45001:2018 Occupational Health and Safety', 'ISO 45001:2018', 'Occupational health and safety', 'How an organisation prevents injury and ill health among the people who work for it.', 500],
            ['ISO/IEC 27001:2022 Information Security', 'ISO/IEC 27001:2022', 'Information security', 'How an organisation protects the information it holds, on paper and in its systems.', 900],
            ['Food Safety and Hygiene', 'Food Safety and Hygiene', 'R638 (South Africa) and S.I. 5 of 2015 (Zimbabwe)', 'Hygiene requirements for food premises and for the transport of food, under South African and Zimbabwean regulations.', 100],
            ['Lead Auditor', null, null, 'Lead auditor training across the management system standards.', 250],
        ];

        foreach ($courses as $i => [$title, $code, $category, $summary, $fee]) {
            Course::updateOrCreate(
                ['slug' => Str::slug($title)],
                [
                    'title' => $title,
                    'code' => $code,
                    'category' => $category,
                    'summary' => $summary,
                    'fee' => $fee,
                    'delivery' => 'E-learning',
                    'start_date' => $start,
                    'is_published' => true,
                    'sort_order' => ($i + 1) * 10,
                ]
            );
        }

        // Placeholders: replace the titles, descriptions and covers in the admin panel.
        Publication::firstOrCreate(
            ['type' => 'book'],
            ['title' => '[BOOK TITLE]', 'description' => '[Short description of the book and its author]', 'price' => 40, 'sort_order' => 10]
        );
        Publication::firstOrCreate(
            ['type' => 'magazine'],
            ['title' => '[MAGAZINE TITLE]', 'description' => '[Short description of the magazine and how often it is published]', 'price' => 4.99, 'sort_order' => 20]
        );
    }
}
