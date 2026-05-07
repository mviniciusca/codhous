<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('social_posts', function (Blueprint $table) {
            $table->string('text_align')->default('center')->after('text_y');
            $table->boolean('is_bold')->default(true)->after('text_align');
            $table->boolean('is_italic')->default(false)->after('is_bold');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('social_posts', function (Blueprint $table) {
            $table->dropColumn(['text_align', 'is_bold', 'is_italic']);
        });
    }
};
