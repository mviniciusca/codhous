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
        Schema::table('budgets', function (Blueprint $table) {
            $table->timestamp('status_changed_at')->nullable()->after('status')
                ->comment('Quando o orçamento entrou na etapa atual');
            $table->index(['status', 'status_changed_at']);
        });

        // Melhor aproximação disponível para os registros existentes
        DB::table('budgets')->update(['status_changed_at' => DB::raw('updated_at')]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('budgets', function (Blueprint $table) {
            $table->dropIndex(['status', 'status_changed_at']);
            $table->dropColumn('status_changed_at');
        });
    }
};
