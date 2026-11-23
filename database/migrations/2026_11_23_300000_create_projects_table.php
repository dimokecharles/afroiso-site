<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Consulting projects: one per engagement, belonging to a client contact. Invoices can be linked to a project. */
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('standard', 80)->nullable();
            $table->string('stage', 30)->default('proposal')->index();
            $table->decimal('value', 12, 2)->nullable();
            $table->date('start_date')->nullable();
            $table->date('target_date')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('orders', fn (Blueprint $table) => $table->dropConstrainedForeignId('project_id'));
        Schema::dropIfExists('projects');
    }
};
