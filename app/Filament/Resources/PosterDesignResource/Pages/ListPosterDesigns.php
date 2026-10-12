<?php

namespace App\Filament\Resources\PosterDesignResource\Pages;

use App\Filament\Resources\PosterDesignResource;
use App\Models\PosterDesign;
use App\Services\PosterStudio\BrandKit;
use App\Services\PosterStudio\Formats;
use App\Services\PosterStudio\Palettes;
use App\Services\PosterStudio\PosterDocument;
use App\Services\PosterStudio\Templates;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;
use InvalidArgumentException;

class ListPosterDesigns extends ListRecords
{
    protected static string $resource = PosterDesignResource::class;

    protected static string $view = 'filament.resources.poster-design-resource.pages.list';

    public function getTitle(): string|Htmlable
    {
        return __('Estúdio de Posts');
    }

    public function getSubheading(): ?string
    {
        return __('Crie artes para Instagram, WhatsApp e Facebook com a identidade da sua empresa.');
    }

    /**
     * Cria um projeto a partir de um template e abre o estúdio.
     */
    public function createDesign(string $templateId, string $format, ?int $width = null, ?int $height = null): void
    {
        $template = Templates::find($templateId);

        if (! $template || ! Formats::exists($format)) {
            Notification::make()->title(__('Template ou formato inválido.'))->danger()->send();

            return;
        }

        try {
            $document = PosterDocument::fromTemplate($template, $format, Palettes::default(), new BrandKit, $width, $height);
        } catch (InvalidArgumentException $e) {
            Notification::make()->title($e->getMessage())->danger()->send();

            return;
        }

        $design = PosterDesign::create([
            'user_id' => auth()->id(),
            'name' => $template['name'].' · '.now()->format('d/m H:i'),
            'template_id' => $template['id'],
            'format' => $format,
            'width' => $document['width'],
            'height' => $document['height'],
            'palette_id' => $document['palette']['id'],
            'document' => $document,
        ]);

        $this->redirect(PosterDesignResource::getUrl('studio', ['record' => $design]));
    }

    protected function getViewData(): array
    {
        $brand = new BrandKit;

        return [
            'templates' => collect(Templates::all())->map(fn (array $template) => [
                'id' => $template['id'],
                'name' => $template['name'],
                'category' => $template['category'] ?? 'Geral',
                'description' => $template['description'] ?? '',
                'document' => PosterDocument::fromTemplate($template, 'instagram_square', null, $brand),
            ])->values()->all(),
            'formats' => Formats::all(),
            'customLimits' => config('poster-studio.custom_format'),
            'fonts' => config('poster-studio.fonts'),
        ];
    }
}
