<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('poster_designs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('poster_designs')->nullOnDelete();
            $table->string('name');
            $table->string('variant_label')->nullable();
            $table->string('template_id')->nullable();
            $table->string('format');
            $table->unsignedInteger('width');
            $table->unsignedInteger('height');
            $table->string('palette_id')->nullable();
            $table->json('document');
            $table->string('thumbnail')->nullable();
            $table->unsignedInteger('revision')->default(1);
            $table->timestamps();
        });

        Schema::create('poster_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('disk');
            $table->string('path');
            $table->string('mime_type');
            $table->unsignedInteger('width');
            $table->unsignedInteger('height');
            $table->unsignedBigInteger('size');
            $table->string('category')->default('upload');
            $table->timestamps();
        });

        Schema::create('poster_palettes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->json('colors');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('poster_palettes');
        Schema::dropIfExists('poster_assets');
        Schema::dropIfExists('poster_designs');
    }
};
