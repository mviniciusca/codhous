<?php

use App\Filament\Resources\PosterDesignResource\Pages\ListPosterDesigns;
use App\Filament\Resources\PosterDesignResource\Pages\PosterStudio;
use App\Models\PosterAsset;
use App\Models\PosterDesign;
use App\Models\Setting;
use App\Models\User;
use App\Services\PosterStudio\BrandKit;
use App\Services\PosterStudio\FormatAdapter;
use App\Services\PosterStudio\Formats;
use App\Services\PosterStudio\Palettes;
use App\Services\PosterStudio\PosterDocument;
use App\Services\PosterStudio\Templates;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    Storage::fake('public');

    // Setting::current() memoiza a linha em propriedade estática; zera entre testes
    Closure::bind(fn () => Setting::$current_instance = null, null, Setting::class)();

    Setting::create(['settings' => [
        'website' => ['primary_color' => '255 90 31', 'social_networks' => ['whatsapp' => 'https://wa.me/5521911112222', 'instagram' => 'https://instagram.com/concreteira']],
        'company' => ['trade_name' => 'Concreteira Teste', 'phone' => '(21) 3333-4444'],
    ]]);

    $this->actingAs(User::factory()->create());
});

function textElement(array $overrides = []): array
{
    return array_merge([
        'id' => 'el_title', 'type' => 'text', 'name' => 'Título', 'x' => 10, 'y' => 20, 'width' => 400, 'height' => 80,
        'text' => 'Olá', 'fontFamily' => 'Montserrat', 'fontSize' => 60, 'fontWeight' => 800, 'fill' => ['role' => 'primary'],
    ], $overrides);
}

function blankDocument(array $elements = []): array
{
    $document = PosterDocument::blank('instagram_square', Palettes::find('laranja'));
    $document['elements'] = $elements;

    return $document;
}

// ------------------------------------------------------------------ documento

test('todos os templates geram documentos válidos em todos os formatos', function () {
    $brand = new BrandKit;

    foreach (Templates::all() as $template) {
        foreach (array_keys(Formats::all()) as $format) {
            $document = PosterDocument::fromTemplate($template, $format, null, $brand);
            $dimensions = Formats::dimensions($format);

            expect($document['width'])->toBe($dimensions['width'])
                ->and($document['height'])->toBe($dimensions['height'])
                ->and($document['elements'])->toHaveCount(count($template['elements']))
                ->and(PosterDocument::validate($document))->toEqual($document);
        }
    }
});

test('elementos vinculados recebem os dados reais da empresa', function () {
    $document = PosterDocument::fromTemplate(Templates::find('concreto-destaque'), 'instagram_square', null, new BrandKit);
    $byId = collect($document['elements'])->keyBy('id');

    expect($byId['phone']['text'])->toBe('(21) 3333-4444')
        ->and($byId['qr']['value'])->toBe('https://wa.me/5521911112222')
        ->and($document['caption'])->toContain('(21) 3333-4444');
});

test('dados ausentes não são inventados', function () {
    expect((new BrandKit)->value('brand.email'))->toBeNull()
        ->and((new BrandKit)->value('brand.whatsapp'))->toBe('(21) 91111-2222')
        ->and((new BrandKit)->value('brand.instagram'))->toBe('@concreteira');
});

test('validação descarta propriedades desconhecidas e normaliza valores', function () {
    $document = PosterDocument::validate(blankDocument([
        textElement(['onclick' => 'alert(1)', 'fontFamily' => 'Comic Sans', 'opacity' => 3, 'fill' => '#00ff00']),
    ]));

    $element = $document['elements'][0];

    expect($element)->not->toHaveKey('onclick')
        ->and($element['fontFamily'])->toBe('Montserrat')
        ->and($element['opacity'])->toEqual(1)
        ->and($element['fill'])->toBe('#00FF00');
});

test('validação rejeita tipos desconhecidos e formatos inválidos', function () {
    expect(fn () => PosterDocument::validate(blankDocument([['id' => 'x', 'type' => 'script']])))
        ->toThrow(ValidationException::class);

    expect(fn () => PosterDocument::validate([...blankDocument(), 'format' => 'tiktok']))
        ->toThrow(ValidationException::class);
});

test('imagens externas ou base64 não entram no documento', function () {
    $image = ['id' => 'img', 'type' => 'image', 'x' => 0, 'y' => 0, 'width' => 100, 'height' => 100];

    $document = PosterDocument::validate(blankDocument([
        [...$image, 'id' => 'a', 'src' => 'https://evil.example/foto.jpg'],
        [...$image, 'id' => 'b', 'src' => 'data:image/png;base64,AAAA'],
        [...$image, 'id' => 'c', 'src' => '/storage/poster-studio/assets/foto.jpg'],
    ]));

    expect(collect($document['elements'])->pluck('src')->all())
        ->toBe([null, null, '/storage/poster-studio/assets/foto.jpg']);
});

test('trocar a paleta preserva cores personalizadas', function () {
    $document = PosterDocument::validate(blankDocument([
        textElement(['id' => 'linked']),
        textElement(['id' => 'custom', 'fill' => '#25D366']),
    ]));

    $green = PosterDocument::withPalette($document, Palettes::find('verde'));

    expect(PosterDocument::resolveColor($green['elements'][0]['fill'], $green))->toBe('#159447')
        ->and(PosterDocument::resolveColor($green['elements'][1]['fill'], $green))->toBe('#25D366')
        ->and($green['elements'][0]['text'])->toBe($document['elements'][0]['text']);
});

test('paleta da marca é derivada da cor primária do site', function () {
    expect(Palettes::brand()['colors']['primary'])->toBe('#FF5A1F');
});

// ------------------------------------------------------------------ formatos

test('adaptar formato estica fundos e não distorce os demais elementos', function () {
    $document = PosterDocument::validate(blankDocument([
        ['id' => 'bg', 'type' => 'rect', 'x' => 0, 'y' => 0, 'width' => 1080, 'height' => 1080],
        ['id' => 'badge', 'type' => 'rect', 'x' => 60, 'y' => 60, 'width' => 200, 'height' => 100],
        ['id' => 'cta', 'type' => 'rect', 'x' => 60, 'y' => 900, 'width' => 300, 'height' => 120],
    ]));

    $story = FormatAdapter::adapt($document, 'story');
    $byId = collect($story['elements'])->keyBy('id');

    expect($byId['bg'])->toMatchArray(['width' => 1080.0, 'height' => 1920.0])
        ->and($byId['badge'])->toMatchArray(['x' => 60.0, 'y' => 60.0, 'width' => 200.0, 'height' => 100.0])
        // elemento do terço inferior continua ancorado à base, com a mesma margem
        ->and($byId['cta']['y'] + $byId['cta']['height'])->toEqual(1920 - 60);
});

test('layout específico do template tem prioridade ao mudar de formato', function () {
    $template = Templates::find('concreto-destaque');
    $document = PosterDocument::fromTemplate($template, 'story', null, new BrandKit);
    $photo = collect($document['elements'])->firstWhere('id', 'photo');

    expect($photo)->toMatchArray($template['layouts']['story']['photo']);
});

test('formato personalizado respeita os limites', function () {
    expect(Formats::dimensions('custom', 1500, 500))->toMatchArray(['width' => 1500, 'height' => 500])
        ->and(fn () => Formats::dimensions('custom', 50, 50))->toThrow(InvalidArgumentException::class);
});

// ------------------------------------------------------------------ páginas

test('galeria abre e cria um projeto a partir de um template', function () {
    livewire(ListPosterDesigns::class)->assertOk()->assertSee('Concreto em destaque');

    livewire(ListPosterDesigns::class)
        ->call('createDesign', 'bombeamento', 'instagram_portrait')
        ->assertRedirect();

    $design = PosterDesign::sole();

    expect($design->template_id)->toBe('bombeamento')
        ->and([$design->width, $design->height])->toBe([1080, 1350])
        ->and($design->document['elements'])->not->toBeEmpty();
});

test('estúdio salva o documento e a miniatura, e detecta conflito de versão', function () {
    $design = createDesign();
    livewire(PosterStudio::class, ['record' => $design->id])->assertOk()->assertSee('posterStudio');

    $document = $design->document;
    $titleIndex = collect($document['elements'])->search(fn ($el) => $el['id'] === 'title');
    $document['elements'][$titleIndex]['text'] = 'Novo título';
    $thumbnail = 'data:image/jpeg;base64,'.base64_encode(tinyJpeg());

    livewire(PosterStudio::class, ['record' => $design->id])
        ->call('save', $document, 1, 'Campanha de outubro', $thumbnail);

    $design->refresh();

    expect($design->revision)->toBe(2)
        ->and($design->name)->toBe('Campanha de outubro')
        ->and($design->document['elements'][$titleIndex]['text'])->toBe('Novo título')
        ->and($design->thumbnail)->not->toBeNull();

    Storage::disk('public')->assertExists($design->thumbnail);

    // Outra aba ainda na revisão 1
    livewire(PosterStudio::class, ['record' => $design->id])->call('save', $document, 1);
    expect($design->fresh()->revision)->toBe(2);
});

test('salvar com documento inválido não altera o projeto', function () {
    $design = createDesign();

    livewire(PosterStudio::class, ['record' => $design->id])
        ->call('save', [...$design->document, 'format' => 'invalido'], 1);

    expect($design->fresh()->revision)->toBe(1);
});

test('variação de cores cria uma cópia vinculada sem alterar a original', function () {
    $design = createDesign();

    livewire(PosterStudio::class, ['record' => $design->id])
        ->call('createVariation', $design->document, 'azul');

    $variation = PosterDesign::where('parent_id', $design->id)->sole();

    expect($variation->palette_id)->toBe('azul')
        ->and($variation->variant_label)->toBe('Azul')
        ->and($variation->document['palette']['colors']['primary'])->toBe('#1769FF')
        ->and($design->fresh()->palette_id)->not->toBe('azul');

    livewire(PosterStudio::class, ['record' => $variation->id])->call('setAsMain');

    expect($variation->fresh()->parent_id)->toBeNull()
        ->and($design->fresh()->parent_id)->toBe($variation->id);
});

test('duplicar cria um projeto independente', function () {
    $design = createDesign();

    livewire(PosterStudio::class, ['record' => $design->id])->call('duplicate')->assertRedirect();

    expect(PosterDesign::count())->toBe(2)
        ->and(PosterDesign::latest('id')->first()->document)->toEqual($design->document);
});

test('upload aceita imagens e recusa outros arquivos', function () {
    $design = createDesign();

    livewire(PosterStudio::class, ['record' => $design->id])
        ->set('upload', UploadedFile::fake()->image('obra.jpg', 1200, 800))
        ->call('storeUpload');

    $asset = PosterAsset::sole();
    expect([$asset->width, $asset->height])->toBe([1200, 800])
        ->and($asset->url())->toStartWith('/storage/');
    Storage::disk('public')->assertExists($asset->path);

    livewire(PosterStudio::class, ['record' => $design->id])
        ->set('upload', UploadedFile::fake()->create('script.php', 10, 'text/x-php'))
        ->call('storeUpload');

    expect(PosterAsset::count())->toBe(1);

    livewire(PosterStudio::class, ['record' => $design->id])->call('deleteAsset', $asset->id);
    Storage::disk('public')->assertMissing($asset->path);
});

test('paletas personalizadas podem ser salvas e excluídas', function () {
    $design = createDesign();

    livewire(PosterStudio::class, ['record' => $design->id])
        ->call('savePalette', 'Minha marca', ['primary' => '#123456', 'text' => 'vermelho']);

    $palette = collect(Palettes::all())->firstWhere('name', 'Minha marca');

    expect($palette['colors']['primary'])->toBe('#123456')
        ->and($palette['colors']['text'])->toBe('#111827');

    livewire(PosterStudio::class, ['record' => $design->id])->call('deletePalette', $palette['id']);

    expect(Palettes::find($palette['id']))->toBeNull();
});

function createDesign(): PosterDesign
{
    $document = PosterDocument::fromTemplate(Templates::find('comunicado'), 'instagram_square', null, new BrandKit);

    return PosterDesign::create([
        'user_id' => auth()->id(),
        'name' => 'Comunicado',
        'template_id' => 'comunicado',
        'format' => 'instagram_square',
        'width' => 1080,
        'height' => 1080,
        'palette_id' => $document['palette']['id'],
        'document' => $document,
    ]);
}

function tinyJpeg(): string
{
    $image = imagecreatetruecolor(8, 8);
    ob_start();
    imagejpeg($image);

    return ob_get_clean();
}
