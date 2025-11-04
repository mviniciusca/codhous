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
        Schema::table('product_options', function (Blueprint $table) {
            // Remover a constraint antiga de unique no campo name
            $table->dropUnique('product_options_name_unique');

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
