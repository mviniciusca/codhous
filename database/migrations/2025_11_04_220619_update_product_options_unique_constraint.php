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
        $isMysql = DB::getDriverName() === 'mysql';
        $constraints = [];

        if ($isMysql) {
            // Usar SQL raw para verificar e remover constraint com segurança no MySQL
            $constraints = DB::select("
                SELECT CONSTRAINT_NAME
                FROM information_schema.TABLE_CONSTRAINTS
                WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = 'product_options'
                AND CONSTRAINT_NAME = 'product_options_name_unique'
            ");
        }

        Schema::table('product_options', function (Blueprint $table) use ($constraints, $isMysql) {
            // Remover a constraint antiga se ela existir
            if ($isMysql && ! empty($constraints)) {
                $table->dropUnique('product_options_name_unique');
            }

            // No SQLite, se estivermos criando a tabela do zero nos testes, 
            // a constraint composta é adicionada normalmente abaixo.
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
