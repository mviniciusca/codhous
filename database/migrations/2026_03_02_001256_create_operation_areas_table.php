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
        Schema::create('operation_areas', function (Blueprint $table) {
            $table->id();
            $table->string('city');
            $table->string('state')->default('RJ');
            $table->string('postcode_prefix', 5); // Ex: 25000 (primeiros 5 dígitos)
            $table->boolean('is_active')->default(true);
            $table->decimal('shipping_fee', 10, 2)->default(0); // Dica bônus
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operation_areas');
    }
};
