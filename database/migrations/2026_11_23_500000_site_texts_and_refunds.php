<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Editable website text, and a record of when a payment was refunded. */
    public function up(): void
    {
        Schema::create('site_texts', function (Blueprint $table) {
            $table->id();
            $table->string('key', 80)->unique();
            $table->longText('value')->nullable();
            $table->timestamps();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->timestamp('refunded_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('orders', fn (Blueprint $table) => $table->dropColumn('refunded_at'));
        Schema::dropIfExists('site_texts');
    }
};
