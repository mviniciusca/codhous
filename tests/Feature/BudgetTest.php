<?php

use App\Models\Budget;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('orçamento gera código automaticamente ao ser criado', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $budget = Budget::create([
        'content' => ['customer_name' => 'Cliente Teste'],
        'status' => 'pending',
    ]);

    expect($budget->code)->not->toBeNull();
    expect($budget->code)->toStartWith('OR-' . date('Y'));
});

test('orçamento pode ter itens associados', function () {
    $budget = Budget::factory()->create();
    expect($budget->budgetItems)->toBeObject();
});
