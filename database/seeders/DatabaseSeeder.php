<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Publication;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

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
            ['iso-9001-quality-management', 'ISO 9001:2026 Quality Management Systems (QMS)', 'ISO 9001:2026', 'Quality Management Systems', 'How an organisation consistently delivers what its customers and regulators require.', 500],
            ['iso-14001-environmental-management', 'ISO 14001:2015 Environmental Management Systems (EMS)', 'ISO 14001:2015', 'Environmental Management Systems', 'How an organisation controls its effect on the environment and meets its legal duties.', 500],
            ['iso-45001-occupational-health-and-safety', 'ISO 45001:2018 Occupational Health and Safety Management Systems (OHSMS)', 'ISO 45001:2018', 'Occupational Health and Safety Management Systems', 'How an organisation prevents injury and ill health among the people who work for it.', 500],
            ['iso-iec-27001-information-security', 'ISO/IEC 27001:2022 Information Security Management Systems (ISMS)', 'ISO/IEC 27001:2022', 'Information Security Management Systems', 'How an organisation protects the information it holds, on paper and in its systems.', 900],
            ['food-safety-and-hygiene', 'Food Safety and Hygiene', 'Food Safety and Hygiene', 'R638 (South Africa) and S.I. 5 of 2015 (Zimbabwe)', 'Hygiene requirements for food premises and for the transport of food, under South African and Zimbabwean regulations.', 100],
            ['internal-auditor', 'Internal Auditor', null, 'Auditing', 'Internal auditor training across the management system standards.', 250],
        ];

        foreach ($courses as $i => [$slug, $title, $code, $category, $summary, $fee]) {
            Course::updateOrCreate(
                ['slug' => $slug],
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

        $publications = [
            ['book', 'Systems Over Hustle: ISO Standards for African SMEs', "By Norman Sifiso Ndlovu.\nHow African small and medium-sized enterprises can use ISO standards to build systems that run the business, instead of relying on hustle.", 40, 'images/publications/systems-over-hustle.jpg', 10],
            ['magazine', 'StandardIQ, 1–15 December 2026: Ready for 2027?', "AfroISO's fortnightly ISO magazine.\nIn this issue: a more inclusive future, with Norah Kimathi, co-founder of Zerobionic in Kenya, on robotics, learning and African ingenuity. Also: quality decisions for the year ahead.", 4.99, 'images/publications/standardiq-december-2026.webp', 12],
            ['magazine', 'StandardIQ, 15–30 November 2026: The Price of Trust', "AfroISO's fortnightly ISO magazine.\nIn this issue: Simbarashe Mhuriro on building businesses that earn confidence. Also: African enterprise competing on quality, and what a quality mark really tells you.", 4.99, 'images/publications/standardiq-november-15-30-2026.webp', 16],
            ['magazine', 'StandardIQ, 1–15 November 2026: The Quality Leadership Test', "AfroISO's fortnightly ISO magazine.\nIn this issue: South Africa adopts ISO 9001:2026, and Blake Mosley-Lefatola, CEO of SABS, on turning standards into better performance.", 4.99, 'images/publications/standardiq-november-2026.webp', 20],
            ['magazine', 'StandardIQ, 5 October – 4 November 2026: ISO 9001:2026, the New Quality Era Begins', "AfroISO's fortnightly ISO magazine.\nIn this issue: the new edition of ISO 9001, featuring Khaled Soufi (ISO President), Francisca Tatchouop Belobe (AU Commissioner for ETTIM) and Hermogène Nsengimana (Secretary General, ARSO).", 4.99, 'images/publications/standardiq-october-2026.webp', 30],
        ];

        foreach ($publications as [$type, $title, $description, $price, $coverPath, $sortOrder]) {
            Publication::firstOrCreate(
                ['title' => $title],
                ['type' => $type, 'description' => $description, 'price' => $price, 'cover_path' => $coverPath, 'sort_order' => $sortOrder]
            );
        }
    }
}
