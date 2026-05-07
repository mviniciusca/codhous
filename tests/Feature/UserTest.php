<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('usuário pode ser criado com sucesso', function () {
    $user = User::factory()->create([
        'name' => 'Teste Antigravity',
        'email' => 'teste@exemplo.com',
    ]);

    $this->assertDatabaseHas('users', [
        'email' => 'teste@exemplo.com',
    ]);
});

test('usuário pode ter relacionamentos', function () {
    $user = User::factory()->create();
    
    expect($user->budgets)->toBeObject();
    expect($user->mails)->toBeObject();
});
