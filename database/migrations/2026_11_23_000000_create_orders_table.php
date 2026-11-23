<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * One table for everything that is paid for: course enrolments, publication orders and consulting invoices.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 30)->nullable()->unique();
            $table->string('token', 64)->unique();
            $table->string('type', 20);
            $table->foreignId('course_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('publication_id')->nullable()->constrained()->nullOnDelete();
            $table->string('description');
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone', 60)->nullable();
            $table->string('organisation')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->string('status', 20)->default('pending')->index();
            $table->string('gateway', 40)->nullable();
            $table->string('gateway_reference')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
