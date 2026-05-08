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
        Schema::table('social_posts', function (Blueprint $column) {
            $column->integer('pattern_size')->default(10)->after('pattern');
            $column->string('pattern_color')->default('#ffffff')->after('pattern_size');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('social_posts', function (Blueprint $column) {
            $column->dropColumn(['pattern_size', 'pattern_color']);
        });
    }
};
