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
            $column->integer('font_size')->default(80)->after('font_family');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('social_posts', function (Blueprint $column) {
            $column->dropColumn('font_size');
        });
    }
};
