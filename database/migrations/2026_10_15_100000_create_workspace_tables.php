<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Meu Workspace: tarefas, compromissos e notas rápidas de cada usuário.
 * Datas e horários são guardados como o usuário digitou (horário local), sem fuso.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workspace_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title', 160);
            $table->date('due_date')->nullable();
            $table->string('due_time', 5)->nullable();
            $table->string('priority', 10)->default('medium');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'completed_at', 'due_date']);
        });

        Schema::create('workspace_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title', 160);
            $table->date('date');
            $table->boolean('all_day')->default(false);
            $table->string('starts_at', 5)->nullable();
            $table->string('ends_at', 5)->nullable();
            $table->string('location', 160)->nullable();
            $table->string('color', 10)->default('primary');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'date']);
        });

        Schema::create('workspace_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title', 120);
            $table->text('content')->nullable();
            $table->string('color', 10)->default('gray');
            $table->timestamps();

            $table->index(['user_id', 'updated_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workspace_notes');
        Schema::dropIfExists('workspace_events');
        Schema::dropIfExists('workspace_tasks');
    }
};
