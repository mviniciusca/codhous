<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mails', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('name');
            $table->string('phone');
            $table->string('subject');
            $table->text('message');
            $table->boolean('is_sent')->default(false);
            $table->boolean('is_read')->default(false);
            $table->boolean('is_favorite')->default(false);
            $table->boolean('is_spam')->default(false);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mails');
    }
};
