<?php

namespace App\Filament\Resources\PosterDesignResource\Pages;

use App\Filament\Resources\PosterDesignResource;
use App\Models\PosterAsset;
use App\Models\PosterDesign;
use App\Models\PosterPalette;
use App\Services\PosterStudio\BrandKit;
use App\Services\PosterStudio\FormatAdapter;
use App\Services\PosterStudio\Formats;
use App\Services\PosterStudio\Palettes;
use App\Services\PosterStudio\PosterDocument;
use App\Services\PosterStudio\Templates;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use Livewire\Attributes\Renderless;

/**
 * Editor em tela cheia. O canvas roda no navegador (Fabric.js, resources/js/poster-studio);
 * aqui ficam a persistência e as regras que precisam do servidor. Todos os métodos
 * chamados pelo editor são #[Renderless]: o estado do canvas nunca passa pelo re-render.
 */
class PosterStudio extends Page
{
    use InteractsWithRecord;

    protected static string $resource = PosterDesignResource::class;

    protected static string $view = 'filament.resources.poster-design-resource.pages.studio';

    protected static string $layout = 'filament-panels::components.layout.base';

    public $upload;

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
    }

    public function getTitle(): string|Htmlable
    {
        return $this->record->name;
    }

    #[Renderless]
    public function save(array $document, int $revision, ?string $name = null, ?string $thumbnail = null, bool $force = false): array
    {
        $design = $this->record->fresh();

        if (! $force && $design->revision !== $revision) {
            return [
                'ok' => false,
                'conflict' => true,
                'message' => __('Esta arte foi alterada em outra aba ou por outra pessoa.'),
            ];
        }

        try {
            $document = PosterDocument::validate($document);
        } catch (ValidationException $e) {
            return ['ok' => false, 'message' => collect($e->errors())->flatten()->first()];
        }

        $design->fill([
            'name' => $name !== null && trim($name) !== '' ? Str::limit(strip_tags(trim($name)), 120, '') : $design->name,
            'document' => $document,
            'format' => $document['format'],
            'width' => $document['width'],
            'height' => $document['height'],
            'palette_id' => $document['palette']['id'],
            'template_id' => $document['templateId'],
            'revision' => $design->revision + 1,
        ]);

        if ($thumbnail !== null) {
            $design->thumbnail = $this->storeThumbnail($design, $thumbnail) ?? $design->thumbnail;
        }

        $design->save();
        $this->record = $design;

        return [
            'ok' => true,
            'revision' => $design->revision,
            'savedAt' => $design->updated_at->format('H:i'),
            'name' => $design->name,
        ];
    }

    /**
     * Adapta o documento a outro formato (ver FormatAdapter). Não persiste.
     */
    #[Renderless]
    public function adaptFormat(array $document, string $format, ?int $width = null, ?int $height = null): array
    {
        try {
            $document = PosterDocument::validate($document);

            return ['ok' => true, 'document' => FormatAdapter::adapt($document, $format, Templates::find($document['templateId']), $width, $height)];
        } catch (InvalidArgumentException|ValidationException $e) {
            return ['ok' => false, 'message' => $e instanceof ValidationException ? collect($e->errors())->flatten()->first() : $e->getMessage()];
        }
    }

    /**
     * Novo documento a partir de um template, mantendo formato e paleta atuais. Não persiste.
     */
    #[Renderless]
    public function applyTemplate(array $document, string $templateId): array
    {
        $template = Templates::find($templateId);

        if (! $template) {
            return ['ok' => false, 'message' => __('Template não encontrado.')];
        }

        $current = PosterDocument::validate($document);

        try {
            $new = PosterDocument::fromTemplate($template, $current['format'], $current['palette'], new BrandKit, $current['width'], $current['height']);
        } catch (InvalidArgumentException $e) {
            return ['ok' => false, 'message' => $e->getMessage()];
        }

        return ['ok' => true, 'document' => $new];
    }

    /**
     * Salva uma cópia do documento com outra paleta como variação deste projeto.
     */
    #[Renderless]
    public function createVariation(array $document, string $paletteId, ?array $colors = null): array
    {
        $palette = Palettes::find($paletteId) ?? ($colors ? ['id' => 'custom', 'name' => 'Personalizada', 'colors' => $colors] : null);

        if (! $palette) {
            return ['ok' => false, 'message' => __('Paleta não encontrada.')];
        }

        try {
            $document = PosterDocument::withPalette(PosterDocument::validate($document), $palette);
        } catch (ValidationException $e) {
            return ['ok' => false, 'message' => collect($e->errors())->flatten()->first()];
        }

        $root = $this->record->parent ?? $this->record;

        $variation = PosterDesign::create([
            'user_id' => auth()->id(),
            'parent_id' => $root->id,
            'name' => $root->name,
            'variant_label' => $palette['name'],
            'template_id' => $document['templateId'],
            'format' => $document['format'],
            'width' => $document['width'],
            'height' => $document['height'],
            'palette_id' => $palette['id'],
            'document' => $document,
        ]);

        return ['ok' => true, 'id' => $variation->id, 'url' => PosterDesignResource::getUrl('studio', ['record' => $variation])];
    }

    #[Renderless]
    public function setAsMain(): array
    {
        $design = $this->record->fresh();

        if (! $design->parent_id) {
            return ['ok' => true];
        }

        $oldRoot = $design->parent;

        PosterDesign::where('parent_id', $oldRoot->id)->whereKeyNot($design->id)->update(['parent_id' => $design->id]);
        $oldRoot->update(['parent_id' => $design->id, 'variant_label' => $oldRoot->variant_label ?? __('Original')]);
        $design->update(['parent_id' => null]);

        $this->record = $design;

        return ['ok' => true, 'family' => $this->family()];
    }

    #[Renderless]
    public function family(): array
    {
        $rootId = $this->record->fresh()->rootId();

        return PosterDesign::query()
            ->where(fn ($query) => $query->whereKey($rootId)->orWhere('parent_id', $rootId))
            ->orderByRaw('parent_id is not null')
            ->oldest()
            ->get()
            ->map(fn (PosterDesign $design) => [
                'id' => $design->id,
                'label' => $design->variant_label ?? __('Principal'),
                'main' => $design->parent_id === null,
                'current' => $design->id === $this->record->id,
                'thumbnail' => $design->thumbnailUrl(),
                'url' => PosterDesignResource::getUrl('studio', ['record' => $design]),
            ])
            ->all();
    }

    public function duplicate(): void
    {
        $copy = self::duplicateRecord($this->record->fresh());

        $this->redirect(PosterDesignResource::getUrl('studio', ['record' => $copy]));
    }

    public static function duplicateRecord(PosterDesign $design): PosterDesign
    {
        $copy = $design->replicate(['thumbnail', 'parent_id', 'variant_label']);
        $copy->name = Str::limit($design->name, 100, '').' (cópia)';
        $copy->user_id = auth()->id();
        $copy->revision = 1;
        $copy->save();

        if ($design->thumbnail && Storage::disk('public')->exists($design->thumbnail)) {
            $path = config('poster-studio.thumbnails.directory').'/'.$copy->id.'-'.Str::random(6).'.jpg';
            Storage::disk('public')->copy($design->thumbnail, $path);
            $copy->update(['thumbnail' => $path]);
        }

        return $copy;
    }

    /**
     * Chamado pelo editor depois de $wire.upload('upload', file).
     */
    #[Renderless]
    public function storeUpload(): array
    {
        $config = config('poster-studio.uploads');

        try {
            $this->validate([
                'upload' => [
                    'required', 'file', 'image',
                    'mimes:'.implode(',', $config['mimes']),
                    'max:'.$config['max_kb'],
                    "dimensions:max_width={$config['max_dimension']},max_height={$config['max_dimension']}",
                ],
            ], [], ['upload' => 'imagem']);
        } catch (ValidationException $e) {
            $this->reset('upload');

            return ['ok' => false, 'message' => collect($e->errors())->flatten()->first()];
        }

        // Dimensões lidas do conteúdo real do arquivo, não da extensão
        [$width, $height] = getimagesize($this->upload->getRealPath()) ?: [0, 0];

        $path = $this->upload->store($config['directory'], $config['disk']);

        $asset = PosterAsset::create([
            'user_id' => auth()->id(),
            'name' => Str::limit(pathinfo($this->upload->getClientOriginalName(), PATHINFO_FILENAME), 100, ''),
            'disk' => $config['disk'],
            'path' => $path,
            'mime_type' => $this->upload->getMimeType(),
            'width' => $width,
            'height' => $height,
            'size' => $this->upload->getSize(),
        ]);

        $this->reset('upload');

        return ['ok' => true, 'asset' => $asset->toLibraryArray()];
    }

    #[Renderless]
    public function deleteAsset(int $id): array
    {
        PosterAsset::findOrFail($id)->delete();

        return ['ok' => true];
    }

    #[Renderless]
    public function savePalette(string $name, array $colors): array
    {
        $name = Str::limit(strip_tags(trim($name)), 60, '') ?: __('Minha paleta');

        $palette = PosterPalette::create([
            'user_id' => auth()->id(),
            'name' => $name,
            'colors' => Palettes::normalize($colors),
        ]);

        return ['ok' => true, 'palette' => Palettes::find('custom-'.$palette->id)];
    }

    #[Renderless]
    public function deletePalette(string $paletteId): array
    {
        if (! preg_match('/^custom-(\d+)$/', $paletteId, $m)) {
            return ['ok' => false, 'message' => __('Somente paletas personalizadas podem ser excluídas.')];
        }

        PosterPalette::whereKey((int) $m[1])->delete();

        return ['ok' => true];
    }

    /**
     * Dados recarregados da empresa (Settings/catálogo) para atualizar elementos vinculados.
     */
    #[Renderless]
    public function brand(): array
    {
        return (new BrandKit)->toArray();
    }

    public function studioConfig(): array
    {
        $design = $this->record;
        $brand = new BrandKit;

        return [
            'design' => [
                'id' => $design->id,
                'name' => $design->name,
                'revision' => $design->revision,
                'isVariation' => $design->parent_id !== null,
                'variantLabel' => $design->variant_label,
                'updatedAt' => $design->updated_at?->format('d/m/Y H:i'),
            ],
            'document' => $design->document,
            'brand' => $brand->toArray(),
            'palettes' => array_values(Palettes::all()),
            'colorRoles' => config('poster-studio.color_roles'),
            'formats' => Formats::all(),
            'customLimits' => config('poster-studio.custom_format'),
            'fonts' => config('poster-studio.fonts'),
            'templates' => collect(Templates::all())->map(fn (array $template) => [
                'document' => PosterDocument::fromTemplate($template, 'instagram_square', $design->document['palette'] ?? null, $brand),
                'id' => $template['id'],
                'name' => $template['name'],
                'category' => $template['category'] ?? 'Geral',
                'description' => $template['description'] ?? '',
                'tags' => $template['tags'] ?? [],
                'layouts' => array_keys($template['layouts'] ?? []),
            ])->values()->all(),
            'assets' => PosterAsset::query()->latest()->limit(200)->get()->map->toLibraryArray()->all(),
            'family' => $this->family(),
            'uploads' => [
                'maxKb' => config('poster-studio.uploads.max_kb'),
                'mimes' => config('poster-studio.uploads.mimes'),
            ],
            'urls' => [
                'index' => PosterDesignResource::getUrl('index'),
                'settings' => url('/admin'),
            ],
        ];
    }

    private function storeThumbnail(PosterDesign $design, string $dataUrl): ?string
    {
        if (! preg_match('#^data:image/(jpeg|png|webp);base64,#', $dataUrl, $m)) {
            return null;
        }

        $binary = base64_decode(substr($dataUrl, strlen($m[0])), true);

        if ($binary === false || strlen($binary) > config('poster-studio.thumbnails.max_kb') * 1024) {
            return null;
        }

        $info = @getimagesizefromstring($binary);

        if (! $info || ! in_array($info[2], [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_WEBP], true)) {
            return null;
        }

        $extension = image_type_to_extension($info[2], false);
        $path = config('poster-studio.thumbnails.directory').'/'.$design->id.'.'.($extension === 'jpeg' ? 'jpg' : $extension);

        if ($design->thumbnail && $design->thumbnail !== $path) {
            Storage::disk('public')->delete($design->thumbnail);
        }

        Storage::disk('public')->put($path, $binary);

        return $path;
    }
}
