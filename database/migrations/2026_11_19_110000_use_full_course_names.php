<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * slug => [old title, new title, old category, new category]
     * A row is only changed while it still has the old wording, so edits made in the admin panel are kept.
     */
    private array $courses = [
        'iso-9001-quality-management' => [
            'ISO 9001:2026 Quality Management', 'ISO 9001:2026 Quality Management Systems (QMS)',
            'Quality management', 'Quality Management Systems',
        ],
        'iso-14001-environmental-management' => [
            'ISO 14001:2026 Environmental Management', 'ISO 14001:2026 Environmental Management Systems (EMS)',
            'Environmental management', 'Environmental Management Systems',
        ],
        'iso-45001-occupational-health-and-safety' => [
            'ISO 45001:2018 Occupational Health and Safety', 'ISO 45001:2018 Occupational Health and Safety Management Systems (OHSMS)',
            'Occupational health and safety', 'Occupational Health and Safety Management Systems',
        ],
        'iso-iec-27001-information-security' => [
            'ISO/IEC 27001:2022 Information Security', 'ISO/IEC 27001:2022 Information Security Management Systems (ISMS)',
            'Information security', 'Information Security Management Systems',
        ],
    ];

    public function up(): void
    {
        foreach ($this->courses as $slug => [$oldTitle, $newTitle, $oldCategory, $newCategory]) {
            DB::table('courses')->where('slug', $slug)->where('title', $oldTitle)->update(['title' => $newTitle]);
            DB::table('courses')->where('slug', $slug)->where('category', $oldCategory)->update(['category' => $newCategory]);
        }
    }

    public function down(): void
    {
        foreach ($this->courses as $slug => [$oldTitle, $newTitle, $oldCategory, $newCategory]) {
            DB::table('courses')->where('slug', $slug)->where('title', $newTitle)->update(['title' => $oldTitle]);
            DB::table('courses')->where('slug', $slug)->where('category', $newCategory)->update(['category' => $oldCategory]);
        }
    }
};
