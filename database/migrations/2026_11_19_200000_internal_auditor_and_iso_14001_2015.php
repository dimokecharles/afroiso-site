<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Lead Auditor becomes Internal Auditor, and ISO 14001 is taught to the 2015 edition.
     * Each field is only changed while it still has the old wording, so edits made in the admin panel are kept.
     */
    public function up(): void
    {
        $ems = 'iso-14001-environmental-management';
        DB::table('courses')->where('slug', $ems)->where('title', 'ISO 14001:2026 Environmental Management Systems (EMS)')
            ->update(['title' => 'ISO 14001:2015 Environmental Management Systems (EMS)']);
        DB::table('courses')->where('slug', $ems)->where('code', 'ISO 14001:2026')->update(['code' => 'ISO 14001:2015']);

        if (DB::table('courses')->where('slug', 'internal-auditor')->doesntExist()) {
            DB::table('courses')->where('slug', 'lead-auditor')->where('title', 'Lead Auditor')->update(['title' => 'Internal Auditor']);
            DB::table('courses')->where('slug', 'lead-auditor')
                ->where('summary', 'Lead auditor training across the management system standards.')
                ->update(['summary' => 'Internal auditor training across the management system standards.']);
            DB::table('courses')->where('slug', 'lead-auditor')->update(['slug' => 'internal-auditor']);
        }
    }

    public function down(): void
    {
        $ems = 'iso-14001-environmental-management';
        DB::table('courses')->where('slug', $ems)->where('title', 'ISO 14001:2015 Environmental Management Systems (EMS)')
            ->update(['title' => 'ISO 14001:2026 Environmental Management Systems (EMS)']);
        DB::table('courses')->where('slug', $ems)->where('code', 'ISO 14001:2015')->update(['code' => 'ISO 14001:2026']);

        if (DB::table('courses')->where('slug', 'lead-auditor')->doesntExist()) {
            DB::table('courses')->where('slug', 'internal-auditor')->where('title', 'Internal Auditor')->update(['title' => 'Lead Auditor']);
            DB::table('courses')->where('slug', 'internal-auditor')
                ->where('summary', 'Internal auditor training across the management system standards.')
                ->update(['summary' => 'Lead auditor training across the management system standards.']);
            DB::table('courses')->where('slug', 'internal-auditor')->update(['slug' => 'lead-auditor']);
        }
    }
};
