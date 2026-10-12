<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Histórico de envios da newsletter: um registro por disparo e um por destinatário,
 * para mostrar o andamento, as falhas e permitir retomar ou reenviar só as falhas.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('newsletter_campaigns', function (Blueprint $table) {
            $table->id();
            // Gerada pela tela ao entrar na confirmação: o mesmo clique repetido não cria outro envio
            $table->string('idempotency_key', 64)->unique();
            $table->string('subject');
            $table->longText('body');
            $table->string('banner_path')->nullable();
            // Hash de assunto + mensagem + imagem, para avisar sobre envios repetidos
            $table->string('content_hash', 64)->index();
            $table->string('status', 20)->default('sending')->index();
            $table->unsignedInteger('recipients_count')->default(0);
            $table->unsignedInteger('sent_count')->default(0);
            $table->unsignedInteger('failed_count')->default(0);
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();
        });

        Schema::create('newsletter_campaign_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('newsletter_campaign_id')->constrained(indexName: 'nl_recipients_campaign_foreign')->cascadeOnDelete();
            $table->foreignId('newsletter_id')->nullable()->constrained('newsletters', indexName: 'nl_recipients_subscriber_foreign')->nullOnDelete();
            $table->string('email');
            $table->string('name')->nullable();
            $table->string('status', 20)->default('pending');
            $table->text('error')->nullable();
            $table->unsignedSmallInteger('attempts')->default(0);
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['newsletter_campaign_id', 'status'], 'nl_recipients_campaign_status_index');
            $table->unique(['newsletter_campaign_id', 'email'], 'nl_recipients_campaign_email_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('newsletter_campaign_recipients');
        Schema::dropIfExists('newsletter_campaigns');
    }
};
