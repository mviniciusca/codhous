<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Usar SQL raw para verificar e remover constraint com segurança
        $constraints = DB::select("
            SELECT CONSTRAINT_NAME
            FROM information_schema.TABLE_CONSTRAINTS
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = 'product_options'
            AND CONSTRAINT_NAME = 'product_options_name_unique'
        ");

        Schema::table('product_options', function (Blueprint $table) use ($constraints) {
            // Remover a constraint antiga se ela existir
            if (! empty($constraints)) {
                $table->dropUnique('product_options_name_unique');
            }

            // Adicionar nova constraint composta (product_id + name)
            $table->unique(['product_id', 'name'], 'product_options_product_name_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_options', function (Blueprint $table) {
            // Remover a constraint composta
            $table->dropUnique('product_options_product_name_unique');

            // Restaurar a constraint antiga
            $table->unique('name', 'product_options_name_unique');
        });
    }
};
