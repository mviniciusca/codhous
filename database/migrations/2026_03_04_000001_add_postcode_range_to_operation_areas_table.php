<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Faixa de CEP (compreensão): ex. Rio de Janeiro 20000-23999.
     * Quando preenchidos, o CEP é considerado na área se estiver entre start e end (inclusive).
     */
    public function up(): void
    {
        Schema::table('operation_areas', function (Blueprint $table) {
            $table->string('postcode_start', 5)->nullable()->after('postcode_prefix')->comment('Início da faixa (5 dígitos). Ex: 20000');
            $table->string('postcode_end', 5)->nullable()->after('postcode_start')->comment('Fim da faixa (5 dígitos). Ex: 23999');
        });
    }

    public function down(): void
    {
        Schema::table('operation_areas', function (Blueprint $table) {
            $table->dropColumn(['postcode_start', 'postcode_end']);
        });
    }
};
