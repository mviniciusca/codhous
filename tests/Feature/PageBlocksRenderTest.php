<?php

use App\Models\Page;
use App\Models\Setting;
use App\Support\SectionStyle;
use App\Support\SiteTheme;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    Setting::create(['settings' => ['website' => ['primary_color' => '234 179 8']]]);
    // Setting::current() é memorizado de forma estática; zera para ler a linha deste teste
    Setting::endPreview();
});

// Blocos com conteúdo mínimo; os vazios caem no conteúdo de demonstração de cada seção
dataset('blocos', [
    'stats' => ['stats', ['items' => [['value' => '1.500+', 'label' => 'Obras', 'icon' => 'building']]]],
    'stats sem sobreposição' => ['stats', ['overlap' => false, 'items' => [['value' => '98%', 'label' => 'No prazo']]]],
    'image_with_text' => ['image_with_text', ['title' => 'Sobre nós', 'badge' => 'Quem somos', 'button_text' => 'Saiba mais', 'button_url' => '#', 'mini_stats' => [['title' => '12 anos'], ['title' => 'ISO']]]],
    'data_table' => ['data_table', ['title' => 'Obras', 'rows' => [['col1' => 'Edifício', 'col2' => 'SP', 'col3' => 'warning', 'col4' => 'Jan']]]],
    'featured_testimonial' => ['featured_testimonial', ['quote' => 'Ótimo', 'author' => 'Ana']],
    'hero' => ['hero', ['header' => ['title' => 'Concreto'], 'stats' => [['value' => '10+', 'label' => 'anos']]]],
    'hero clean' => ['hero', ['layout' => 'clean', 'overlay_theme' => 'light']],
    'hero whatsapp' => ['hero', ['layout' => 'whatsapp']],
    'hero_simple' => ['hero_simple', ['title' => 'Olá', 'primaryButtonLabel' => 'Ir']],
    'hero_split' => ['hero_split', ['title' => 'Olá', 'features' => [['item' => 'Rápido']], 'buttonLabel' => 'Ir']],
    'partners' => ['partners', []],
    'partners grid' => ['partners', ['layout' => 'grid', 'header' => ['title' => 'Parceiros', 'alignment' => 'right']]],
    'services' => ['services', []],
    'services slider' => ['services', ['items' => array_fill(0, 5, ['title' => 'Serviço', 'bullets' => ['A', 'B']])]],
    'timeline' => ['timeline', []],
    'faq' => ['faq', []],
    'faq centralizado' => ['faq', ['header' => ['title' => 'Dúvidas', 'alignment' => 'center'], 'items' => [['question' => 'P?', 'answer' => 'R.']]]],
    'testimonials' => ['testimonials', []],
    'testimonials slider' => ['testimonials', ['items' => array_fill(0, 4, ['quote' => 'Bom', 'author_name' => 'Zé', 'stars' => 4])]],
    'team' => ['team', []],
    'coverage' => ['coverage', ['cities' => ['Niterói', 'Rio de Janeiro']]],
    'differentials' => ['differentials', []],
    'contact_banner' => ['contact_banner', []],
    'calculator' => ['calculator', []],
    'payment_offer' => ['payment_offer', ['header' => ['title' => 'Pague como quiser'], 'button_label' => 'Comprar', 'background_color' => 'bg-primary']],
    'payment_offer clicável' => ['payment_offer', ['button_url' => 'https://exemplo.com']],
    'budget_form' => ['budget_form', []],
    'contact_form' => ['contact_form', ['header' => ['title' => 'Contato']]],
    'map' => ['map', []],
    'rich_text' => ['rich_text', ['content' => '<p>Texto</p>']],
    'cta' => ['cta', ['title' => 'Vamos conversar']],
    'cards' => ['cards', ['title' => 'Pilares', 'columns' => '4', 'items' => [['title' => 'A', 'description' => 'B']]]],
    'simple_banner' => ['simple_banner', ['image' => 'banners/x.png', 'link_url' => '/']],
    'fundo escuro' => ['cards', ['background_color' => 'bg-foreground', 'items' => [['title' => 'A', 'description' => 'B']]]],
]);

test('bloco renderiza sem erro', function (string $type, array $data) {
    $page = Page::create(['title' => 'Teste', 'slug' => 'teste', 'is_visible' => true, 'content' => []]);

    $html = Blade::render('<x-render-block :type="$type" :data="$data" :page="$page" />', compact('type', 'data', 'page'));

    expect($html)->not->toBeEmpty();
})->with('blocos');

test('página com vários blocos responde 200', function () {
    Page::create([
        'title' => 'Home',
        'slug' => '/',
        'is_visible' => true,
        'content' => collect(['hero', 'stats', 'services', 'differentials', 'timeline', 'testimonials', 'faq', 'cta', 'map'])
            ->map(fn ($type) => ['type' => $type, 'data' => $type === 'stats' ? ['items' => [['value' => '10', 'label' => 'x']]] : []])
            ->all(),
    ]);

    $this->get('/')
        ->assertOk()
        ->assertSee('--primary-color:234 179 8', false)
        ->assertSee('data-theme="default"', false);
});

test('espaçamento vertical usa o valor do bloco ou o padrão', function () {
    expect(SectionStyle::spacing([], 'md'))->toBe('pt-14 md:pt-20 pb-14 md:pb-20')
        ->and(SectionStyle::spacing(['spacing_top' => 'none', 'spacing_bottom' => 'xl']))->toBe('pt-0 pb-24 md:pb-36')
        ->and(SectionStyle::spacing(['spacing_top' => 'inválido'], 'sm'))->toBe('pt-10 md:pt-12 pb-10 md:pb-12');
});

test('esquema de cor segue o fundo da seção', function () {
    expect(SectionStyle::scheme(['background_color' => 'bg-foreground']))->toBe('dark')
        ->and(SectionStyle::scheme(['background_color' => 'bg-primary text-primary-foreground']))->toBe('primary')
        ->and(SectionStyle::scheme(['background_color' => 'bg-primary/10']))->toBe('light')
        ->and(SectionStyle::scheme(['background_color' => 'bg-white', 'text_color' => 'dark']))->toBe('dark');
});

test('cores claras recebem texto escuro e tom ink mais forte', function () {
    $yellow = SiteTheme::css('234 179 8');
    $blue = SiteTheme::css('59 130 246');

    expect($yellow)->toContain('--color-primary-foreground:oklch(0.2')
        ->toContain('--color-primary-ink-base:var(--color-primary-700)')
        ->and($blue)->toContain('--color-primary-foreground:oklch(1 0 0)')
        ->toContain('--color-primary-ink-base:var(--color-primary-600)')
        ->and(SiteTheme::parse('#3b82f6'))->toBe([59, 130, 246])
        ->and(SiteTheme::parse('lixo'))->toBe([249, 115, 22]);
});
