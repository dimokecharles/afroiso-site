<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('code')->nullable();      // heading on the homepage card, e.g. "ISO 9001:2026"
            $table->string('category')->nullable();  // line under the heading, e.g. "Quality management"
            $table->string('summary', 500)->nullable();
            $table->text('description')->nullable();
            $table->decimal('fee', 10, 2)->nullable();
            $table->string('delivery')->default('E-learning');
            $table->date('start_date')->nullable();
            $table->boolean('is_published')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('publications', function (Blueprint $table) {
            $table->id();
            $table->string('type')->default('book'); // book or magazine
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->string('cover_path')->nullable();
            $table->boolean('is_published')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('enquiries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('organisation')->nullable();
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('interest')->nullable();
            $table->string('subject')->nullable();
            $table->text('message')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });

        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->string('holder_name');
            $table->string('course_title');
            $table->date('issued_on');
            $table->string('status')->default('valid'); // valid or revoked
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
        Schema::dropIfExists('enquiries');
        Schema::dropIfExists('publications');
        Schema::dropIfExists('courses');
    }
};
