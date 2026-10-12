<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            // Estado do editor ainda não publicado (conteúdo, SEO e configurações)
            $table->json('draft')->nullable()->after('meta');
            $table->timestamp('draft_saved_at')->nullable()->after('draft');
            $table->timestamp('published_at')->nullable()->after('draft_saved_at');
        });

        DB::table('pages')->update(['published_at' => DB::raw('updated_at')]);
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn(['draft', 'draft_saved_at', 'published_at']);
        });
    }
};
