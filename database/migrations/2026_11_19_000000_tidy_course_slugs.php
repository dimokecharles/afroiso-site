<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $slugs = [
        'iso-90012026-quality-management' => 'iso-9001-quality-management',
        'iso-140012026-environmental-management' => 'iso-14001-environmental-management',
        'iso-450012018-occupational-health-and-safety' => 'iso-45001-occupational-health-and-safety',
        'isoiec-270012022-information-security' => 'iso-iec-27001-information-security',
    ];

    public function up(): void
    {
        foreach ($this->slugs as $old => $new) {
            if (! DB::table('courses')->where('slug', $new)->exists()) {
                DB::table('courses')->where('slug', $old)->update(['slug' => $new]);
            }
        }
    }

    public function down(): void
    {
        foreach ($this->slugs as $old => $new) {
            DB::table('courses')->where('slug', $new)->update(['slug' => $old]);
        }
    }
};
