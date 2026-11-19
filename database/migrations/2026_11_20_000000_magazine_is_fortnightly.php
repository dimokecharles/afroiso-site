<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private string $old = "AfroISO's monthly ISO magazine.";

    private string $new = "AfroISO's fortnightly ISO magazine.";

    /** StandardIQ comes out every two weeks, so the magazine descriptions say fortnightly, not monthly. */
    public function up(): void
    {
        $this->swap($this->old, $this->new);
    }

    public function down(): void
    {
        $this->swap($this->new, $this->old);
    }

    private function swap(string $from, string $to): void
    {
        $rows = DB::table('publications')->where('description', 'like', '%' . $from . '%')->get(['id', 'description']);

        foreach ($rows as $row) {
            DB::table('publications')->where('id', $row->id)
                ->update(['description' => str_replace($from, $to, $row->description), 'updated_at' => now()]);
        }
    }
};
