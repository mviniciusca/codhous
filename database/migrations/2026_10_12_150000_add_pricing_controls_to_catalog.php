<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Catálogo com controle de preço:
 * - unidade e ordem de exibição no produto (as variações herdam a unidade);
 * - custo e arquivamento (soft delete) nas variações;
 * - itens de orçamento guardam nome, unidade e custo do momento da venda,
 *   e um produto usado em orçamentos não pode mais ser apagado em cascata.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('unit')->nullable()->after('description');
            $table->unsignedInteger('sort')->default(0)->after('is_active');
        });

        Schema::table('product_options', function (Blueprint $table) {
            $table->decimal('cost', 10, 2)->nullable()->after('price')->comment('Custo por unidade, para cálculo de margem');
            $table->unsignedInteger('sort')->default(0)->after('is_active');
            $table->softDeletes();
        });

        Schema::table('budget_product', function (Blueprint $table) {
            $table->string('product_name')->nullable()->after('product_option_id');
            $table->string('option_name')->nullable()->after('product_name');
            $table->string('unit')->nullable()->after('option_name');
            $table->decimal('unit_cost', 10, 2)->nullable()->after('price');

            $table->dropForeign(['product_id']);
            $table->foreign('product_id')->references('id')->on('products')->restrictOnDelete();
        });

        $this->backfill();
    }

    public function down(): void
    {
        Schema::table('budget_product', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
            $table->dropColumn(['product_name', 'option_name', 'unit', 'unit_cost']);
        });

        Schema::table('product_options', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn(['cost', 'sort']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['unit', 'sort']);
        });
    }

    protected function backfill(): void
    {
        // Unidade do produto = unidade mais usada entre as variações
        foreach (DB::table('products')->orderBy('name')->get(['id']) as $index => $product) {
            $unit = DB::table('product_options')
                ->where('product_id', $product->id)
                ->whereNotNull('unit')
                ->select('unit', DB::raw('COUNT(*) as total'))
                ->groupBy('unit')
                ->orderByDesc('total')
                ->value('unit');

            DB::table('products')->where('id', $product->id)->update([
                'unit' => $unit,
                'sort' => $index + 1,
            ]);
        }

        foreach (DB::table('product_options')->orderBy('product_id')->orderBy('price')->get(['id', 'product_id']) as $option) {
            DB::table('product_options')->where('id', $option->id)->update([
                'sort' => DB::table('product_options')
                    ->where('product_id', $option->product_id)
                    ->where('id', '<>', $option->id)
                    ->where(fn ($q) => $q->where('sort', '>', 0))
                    ->count() + 1,
            ]);
        }

        // Fotografa nome e unidade atuais nos itens já vendidos
        $products = DB::table('products')->pluck('name', 'id');
        $options = DB::table('product_options')->get(['id', 'name', 'unit'])->keyBy('id');

        foreach (DB::table('budget_product')->get(['id', 'product_id', 'product_option_id']) as $item) {
            $option = $options->get($item->product_option_id);

            DB::table('budget_product')->where('id', $item->id)->update([
                'product_name' => $products[$item->product_id] ?? null,
                'option_name' => $option?->name,
                'unit' => $option?->unit,
            ]);
        }
    }
};
