<?php

use App\Models\Setting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('company_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Setting::class);
            $table->string('trade_name');
            $table->string('legal_name');
            $table->json('address');
            $table->string('cnpj');
            $table->string('phone');
            $table->string('email');
            $table->text('budget_information');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_settings');
    }
};
