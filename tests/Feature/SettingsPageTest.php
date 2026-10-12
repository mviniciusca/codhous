<?php

use App\Filament\Resources\SettingResource\Pages\EditCompany;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->setting = Setting::create(['settings' => [
        'website' => ['name' => 'Codhous', 'navigation' => [['label' => 'Início', 'url' => '/']]],
        'company' => ['trade_name' => 'Codhous', 'opening_hours' => 'Seg a Sex'],
    ]]);

    $this->actingAs(User::factory()->create());
});

test('a lista de configurações leva direto para a tela de edição', function () {
    $this->get('/admin/settings')
        ->assertRedirect(EditCompany::getUrl(['record' => $this->setting]));
});

test('configurações ficam numa página só, sem abas nem sub-navegação', function () {
    $this->get(EditCompany::getUrl(['record' => $this->setting]))
        ->assertOk()
        ->assertSee('Anti-spam')
        ->assertDontSee('fi-tabs', escape: false)
        ->assertSee('Tudo salvo')
        ->assertDontSee('fi-page-sub-navigation-tabs', escape: false);
});

test('salvar mantém as demais chaves das configurações', function () {
    livewire(EditCompany::class, ['record' => $this->setting->getRouteKey()])
        ->fillForm([
            'settings.company.trade_name' => 'Concreteira Nova',
            'settings.security.maintenance_mode' => true,
            'settings.security.maintenance_message' => 'Voltamos já',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $settings = $this->setting->fresh()->settings;

    expect(data_get($settings, 'company.trade_name'))->toBe('Concreteira Nova')
        ->and(data_get($settings, 'company.opening_hours'))->toBe('Seg a Sex')
        ->and(data_get($settings, 'security.maintenance_message'))->toBe('Voltamos já')
        ->and(data_get($settings, 'website.navigation'))->toBe([['label' => 'Início', 'url' => '/']]);
});

test('locação sem configuração aparece como ativa, como no site', function () {
    livewire(EditCompany::class, ['record' => $this->setting->getRouteKey()])
        ->assertFormSet(['settings.company.equipment_rental.is_active' => true]);
});
