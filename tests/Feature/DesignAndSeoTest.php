<?php

use App\Filament\Pages\DesignAndSeo;
use App\Http\Controllers\DesignPreviewController;
use App\Models\Page;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);

    Setting::create(['id' => 1, 'settings' => [
        'website' => [
            'name' => 'Concreteira Antiga',
            'primary_color' => '249 115 22',
            'navigation' => [
                ['label' => 'Início', 'url' => '/'],
                ['label' => 'Serviços', 'url' => '/servicos'],
                ['label' => 'Contato', 'url' => '/contato'],
            ],
            'scripts' => ['head' => '<script>legado()</script>'],
            'features' => ['whatsapp_widget' => ['number' => '(21) 90000-0000', 'enabled' => false]],
        ],
        'company' => ['name' => 'Codhous Ltda'],
    ]]);
});

test('Setting::merge substitui listas inteiras e mescla o resto', function () {
    $merged = Setting::merge(
        ['website' => ['name' => 'A', 'navigation' => [['label' => '1'], ['label' => '2'], ['label' => '3']]], 'company' => ['name' => 'X']],
        ['website' => ['navigation' => [['label' => '1']]]],
    );

    expect($merged['website']['navigation'])->toBe([['label' => '1']])
        ->and($merged['website']['name'])->toBe('A')
        ->and($merged['company']['name'])->toBe('X');
});

test('salvar persiste o formulário sem apagar outras configurações', function () {
    Livewire::test(DesignAndSeo::class)
        ->set('data.settings.website.name', 'Concreteira Nova')
        ->set('data.settings.website.primary_color', '59 130 246')
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('design-saved');

    $settings = Setting::find(1)->settings;

    expect($settings['website']['name'])->toBe('Concreteira Nova')
        ->and($settings['website']['primary_color'])->toBe('59 130 246')
        ->and($settings['company']['name'])->toBe('Codhous Ltda');
});

test('link removido do menu não volta depois de salvar', function () {
    Livewire::test(DesignAndSeo::class)
        ->set('data.settings.website.navigation', [
            ['label' => 'Início', 'url' => '/'],
        ])
        ->call('save')
        ->assertHasNoErrors();

    expect(Setting::find(1)->settings['website']['navigation'])->toBe([['label' => 'Início', 'url' => '/']]);
});

test('scripts da chave antiga aparecem no campo e passam a ser salvos na chave que o site lê', function () {
    Livewire::test(DesignAndSeo::class)
        ->assertSet('data.settings.website.scripts.head_scripts', '<script>legado()</script>')
        ->call('save')
        ->assertHasNoErrors();

    $scripts = Setting::find(1)->settings['website']['scripts'];

    expect($scripts['head_scripts'])->toBe('<script>legado()</script>')
        ->and($scripts)->not->toHaveKey('head');
});

test('nome do site continua obrigatório', function () {
    Livewire::test(DesignAndSeo::class)
        ->set('data.settings.website.name', '')
        ->call('save')
        ->assertHasErrors(['data.settings.website.name' => 'required']);
});

test('descartar volta ao que está salvo', function () {
    Livewire::test(DesignAndSeo::class)
        ->set('data.settings.website.name', 'Rascunho')
        ->call('discard')
        ->assertSet('data.settings.website.name', 'Concreteira Antiga');

    expect(Setting::find(1)->settings['website']['name'])->toBe('Concreteira Antiga');
});

test('prévia renderiza o site real com o rascunho, sem salvar', function () {
    Page::create(['title' => 'Início', 'slug' => '/', 'content' => [], 'is_visible' => true]);

    $url = Livewire::test(DesignAndSeo::class)
        ->set('data.settings.website.name', 'Nome Em Rascunho')
        ->instance()
        ->previewUrl('/');

    $this->get($url)
        ->assertOk()
        ->assertSee('Nome Em Rascunho')
        ->assertHeader('X-Robots-Tag', 'noindex, nofollow')
        // Scripts de terceiros não rodam na prévia
        ->assertDontSee('legado()', false);

    expect(Setting::find(1)->settings['website']['name'])->toBe('Concreteira Antiga');
});

test('prévia de outro usuário não abre', function () {
    $token = str_repeat('a', 64);
    Cache::put(DesignPreviewController::CACHE_PREFIX.$token, ['user_id' => '999999', 'settings' => []], now()->addMinute());

    $this->get(route('design.preview', ['token' => $token]))->assertNotFound();
});

test('site publicado usa os scripts gravados na chave antiga', function () {
    Page::create(['title' => 'Início', 'slug' => '/', 'content' => [], 'is_visible' => true]);

    $this->get('/')->assertOk()->assertSee('legado()', false);
});

test('logo enviado e ainda não salvo aparece na prévia', function () {
    Storage::fake('public');

    $page = Livewire::test(DesignAndSeo::class)
        ->set('data.settings.website.logo', ['novo' => UploadedFile::fake()->image('logo.png', 200, 60)]);

    $url = $page->instance()->previewUrl('/');
    $token = Str::of(parse_url($url, PHP_URL_PATH))->afterLast('/')->toString();
    $logo = Cache::get(DesignPreviewController::CACHE_PREFIX.$token)['settings']['website']['logo'];

    expect($logo)->toBe('website/preview/'.$this->user->id.'-logo.png');
    Storage::disk('public')->assertExists($logo);
    expect(Setting::find(1)->settings['website'])->not->toHaveKey('logo');
});
