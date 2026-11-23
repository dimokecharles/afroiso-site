<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * CRM: contacts, lead tracking on enquiries, notes, and a simple count of website visits.
     */
    public function up(): void
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable()->unique();
            $table->string('phone', 60)->nullable();
            $table->string('organisation')->nullable();
            $table->string('country', 80)->nullable();
            $table->string('source', 20)->default('manual');
            $table->timestamps();
        });

        Schema::table('enquiries', function (Blueprint $table) {
            $table->foreignId('contact_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status', 20)->default('new')->index();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->date('follow_up_on')->nullable();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('contact_id')->nullable()->constrained()->nullOnDelete();
        });

        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->morphs('notable');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->text('body');
            $table->timestamps();
        });

        Schema::create('page_views', function (Blueprint $table) {
            $table->id();
            $table->string('path', 191);
            $table->string('referrer_host', 191)->nullable();
            $table->string('visitor', 64);
            $table->date('viewed_on')->index();
            $table->timestamp('created_at')->nullable();
            $table->index(['viewed_on', 'path']);
        });

        $this->backfillContacts();
    }

    public function down(): void
    {
        Schema::dropIfExists('page_views');
        Schema::dropIfExists('notes');
        Schema::table('orders', fn (Blueprint $table) => $table->dropConstrainedForeignId('contact_id'));
        Schema::table('enquiries', function (Blueprint $table) {
            $table->dropConstrainedForeignId('contact_id');
            $table->dropConstrainedForeignId('assigned_to');
            $table->dropColumn(['status', 'follow_up_on']);
        });
        Schema::dropIfExists('contacts');
    }

    /** Give every existing enquiry and order a contact, one contact per email address. */
    private function backfillContacts(): void
    {
        $ids = [];
        $contactFor = function (string $name, string $email, ?string $phone, ?string $organisation, string $source, $when) use (&$ids) {
            $key = strtolower(trim($email));
            if ($key === '') {
                return null;
            }

            return $ids[$key] ??= DB::table('contacts')->insertGetId([
                'name' => $name, 'email' => $key, 'phone' => $phone, 'organisation' => $organisation,
                'source' => $source, 'created_at' => $when, 'updated_at' => $when,
            ]);
        };

        foreach (DB::table('enquiries')->orderBy('id')->get() as $row) {
            $id = $contactFor($row->name, $row->email, $row->phone, $row->organisation, 'enquiry', $row->created_at);
            DB::table('enquiries')->where('id', $row->id)->update(['contact_id' => $id, 'status' => $row->is_read ? 'contacted' : 'new']);
        }

        foreach (DB::table('orders')->orderBy('id')->get() as $row) {
            $id = $contactFor($row->customer_name, $row->customer_email, $row->customer_phone, $row->organisation, 'order', $row->created_at);
            DB::table('orders')->where('id', $row->id)->update(['contact_id' => $id]);
        }
    }
};
