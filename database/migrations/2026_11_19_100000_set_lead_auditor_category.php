<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Shown under the course name in the homepage "Upcoming courses" list.
        DB::table('courses')->where('slug', 'lead-auditor')->whereNull('category')->update(['category' => 'Auditing']);
    }

    public function down(): void
    {
        DB::table('courses')->where('slug', 'lead-auditor')->where('category', 'Auditing')->update(['category' => null]);
    }
};
