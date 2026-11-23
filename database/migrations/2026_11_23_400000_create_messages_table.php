<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Message box: every enquiry is a conversation. Replies sent from the admin panel, and replies the
     * customer sends back, are stored as messages on that enquiry.
     */
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enquiry_id')->constrained()->cascadeOnDelete();
            $table->string('direction', 3);
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->text('body');
            $table->string('delivery', 20)->nullable();
            $table->timestamps();
        });

        Schema::table('enquiries', function (Blueprint $table) {
            $table->boolean('awaiting_reply')->default(true)->index();
            $table->timestamp('last_message_at')->nullable();
        });

        // Existing enquiries: the conversation starts with the enquiry itself. Closed ones need no reply.
        DB::table('enquiries')->update(['last_message_at' => DB::raw('created_at')]);
        DB::table('enquiries')->whereIn('status', ['converted', 'closed'])->update(['awaiting_reply' => false]);
    }

    public function down(): void
    {
        Schema::table('enquiries', fn (Blueprint $table) => $table->dropColumn(['awaiting_reply', 'last_message_at']));
        Schema::dropIfExists('messages');
    }
};
