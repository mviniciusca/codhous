<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alerts', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Nome interno (admin)');
            $table->string('type', 20)->index()->comment('modal, toast, banner');
            $table->string('style', 30)->default('info')->index()->comment('info, promo, announcement, consent, warning, success');
            $table->string('title')->nullable();
            $table->text('message');
            $table->string('position', 30)->default('top')->comment('top, bottom, top-left, top-right, bottom-left, bottom-right, center');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_dismissible')->default(true);
            $table->boolean('use_cookie')->default(false)->comment('Ao dispensar, grava cookie para não exibir de novo');
            $table->string('cookie_key', 100)->nullable()->comment('Chave do cookie; se vazio, usa alert_{id}');
            $table->unsignedSmallInteger('cookie_duration_days')->nullable()->comment('Dias para o cookie expirar');
            $table->string('cta_label')->nullable();
            $table->string('cta_url')->nullable();
            $table->timestamp('start_at')->nullable();
            $table->timestamp('end_at')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alerts');
    }
};
