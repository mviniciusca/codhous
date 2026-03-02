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
        Schema::table('operation_areas', function (Blueprint $table) {
            // new flag to mark one or more operation areas as company bases
            $table->boolean('is_base')->default(false)->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('operation_areas', function (Blueprint $table) {
            $table->dropColumn('is_base');
        });
    }
};
