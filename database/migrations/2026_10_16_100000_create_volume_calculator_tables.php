<?php

use App\Services\VolumeCalculator\VolumeFormulaPresets;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Calculadora de volume: fórmulas da empresa, versões imutáveis de cada fórmula
 * (o cálculo salvo aponta para a versão usada) e o histórico de cálculos.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('volume_formulas', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('shape', 20)->default('custom');
            $table->boolean('is_system')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort')->default(0);
            $table->timestamps();
        });

        Schema::create('volume_formula_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('volume_formula_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('version');
            $table->json('definition');
            $table->string('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->nullable();

            $table->unique(['volume_formula_id', 'version']);
        });

        Schema::create('volume_calculations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('volume_formula_version_id')->constrained()->restrictOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            // Integração futura: o cálculo pode ser anexado a um orçamento
            $table->foreignId('budget_id')->nullable()->constrained()->nullOnDelete();
            $table->string('reference')->nullable();
            $table->json('inputs');
            $table->json('result');
            $table->decimal('margin_percent', 5, 2);
            $table->decimal('base_volume', 14, 6);
            $table->decimal('final_volume', 14, 6);
            $table->timestamps();

            $table->index('created_at');
        });

        VolumeFormulaPresets::install();
    }

    public function down(): void
    {
        Schema::dropIfExists('volume_calculations');
        Schema::dropIfExists('volume_formula_versions');
        Schema::dropIfExists('volume_formulas');
    }
};
