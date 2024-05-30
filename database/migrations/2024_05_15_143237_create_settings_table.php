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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();

            $table->string('name');

            $table->string('app_name');
            $table->string('email');
            $table->string('office_hour');

            $table->string('meta_title');
            $table->string('meta_author');
            $table->string('meta_keywords');
            $table->string('meta_description');

            $table->boolean('maintenance_mode')
                ->default(false);
            $table->boolean('discovery_mode')
                ->default(false);

            $table->text('header_scripts')
                ->nullable();
            $table->text('body_scripts')
                ->nullable();
            $table->text('google_tag')
                ->nullable();
            $table->text('google_analytics')
                ->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
