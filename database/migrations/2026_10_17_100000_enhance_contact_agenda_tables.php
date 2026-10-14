<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contact_agenda', function (Blueprint $table) {
            $table->string('status')->default('active')->after('category');
            $table->dateTime('last_contact_at')->nullable()->after('notes');
        });

        Schema::create('contact_interactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_agenda_id')->constrained('contact_agenda')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('type')->default('call'); // meeting, call, email, other
            $table->dateTime('scheduled_at');
            $table->string('status')->default('pending'); // pending, completed, cancelled
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_interactions');

        Schema::table('contact_agenda', function (Blueprint $table) {
            $table->dropColumn(['status', 'last_contact_at']);
        });
    }
};
