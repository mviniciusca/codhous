<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('social_posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('platform')->default('instagram'); // instagram, facebook, linkedin
            $table->text('quote')->nullable();
            $table->string('font_family')->default('Inter');
            $table->string('text_color')->default('#ffffff');
            $table->string('overlay_color')->default('#000000');
            $table->unsignedTinyInteger('overlay_opacity')->default(40); // 0-100
            $table->foreignId('background_image_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status')->default('draft'); // draft, queued, processing, done, failed
            $table->string('output_path')->nullable(); // path to generated PNG/PDF
            $table->timestamp('generated_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('social_posts');
    }
};
