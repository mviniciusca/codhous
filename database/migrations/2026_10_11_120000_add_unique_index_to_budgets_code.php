<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Renomeia códigos duplicados existentes (mantém o primeiro) para permitir o índice único
        $duplicates = DB::table('budgets')
            ->select('code')
            ->groupBy('code')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('code');

        foreach ($duplicates as $code) {
            DB::table('budgets')
                ->where('code', $code)
                ->orderBy('id')
                ->get(['id'])
                ->slice(1)
                ->each(fn ($budget) => DB::table('budgets')
                    ->where('id', $budget->id)
                    ->update(['code' => "{$code}-D{$budget->id}"]));
        }

        Schema::table('budgets', function (Blueprint $table) {
            $table->unique('code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('budgets', function (Blueprint $table) {
            $table->dropUnique(['code']);
        });
    }
};
