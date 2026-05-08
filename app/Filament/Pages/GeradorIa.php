<?php

namespace App\Filament\Pages;

use App\Jobs\GenerateSocialPostImage;
use App\Models\BackgroundImage;
use App\Models\SocialPost;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;

class GeradorIa extends Page implements HasActions, HasForms
{
    use InteractsWithActions, InteractsWithForms;
    protected static ?string $navigationIcon = 'heroicon-o-sparkles';

    protected static ?string $navigationLabel = 'Gerador.IA';

    protected static ?string $slug = 'gerador-ia';

    protected static ?int $navigationSort = 3;

    protected static string $view = 'filament.pages.gerador-ia';

    public function getHeading(): string
    {
        return '';
    }

    // ─── Form State ───────────────────────────────────────────

    public string $postTitle    = '';
    public string $platform     = 'instagram';
    public string $quote        = '';
    public string $fontFamily   = 'Inter';
    public string $textColor    = '#ffffff';
    public int    $fontSize     = 42;
    public string $overlayColor = '#000000';
    public int    $overlayOpacity = 40;
    public ?string $pattern       = null;
    public int    $patternSize    = 10;
    public string $patternColor   = '#ffffff';
    public string $preset         = 'max';
    public float  $textX          = 50.0;
    public float  $textY          = 50.0;
    public string $textAlign      = 'center';
    public bool   $isBold         = true;
    public bool   $isItalic       = false;

    /** @var int|null ID of the selected BackgroundImage */
    public ?int $backgroundImageId = null;

    // ─── Computed ─────────────────────────────────────────────

    #[Computed]
    public function backgrounds(): \Illuminate\Database\Eloquent\Collection
    {
        return BackgroundImage::active()->ordered()->get();
    }

    #[Computed]
    public function selectedBackground(): ?BackgroundImage
    {
        return $this->backgroundImageId
            ? BackgroundImage::find($this->backgroundImageId)
            : null;
    }

    #[Computed]
    public function presetOptions(): array
    {
        return collect(\App\Enums\CardPreset::cases())
            ->mapWithKeys(fn ($case) => [$case->value => $case->label()])
            ->toArray();
    }

    #[Computed]
    public function overlayCss(): string
    {
        $hex = ltrim($this->overlayColor, '#');
        [$r, $g, $b] = sscanf($hex, '%02x%02x%02x');
        $alpha = round($this->overlayOpacity / 100, 2);

        return "rgba({$r},{$g},{$b},{$alpha})";
    }

    #[Computed]
    public function fontOptions(): array
    {
        return [
            'Inter'       => 'Inter',
            'Roboto'      => 'Roboto',
            'Montserrat'  => 'Montserrat',
            'Playfair+Display' => 'Playfair Display',
            'Oswald'      => 'Oswald',
            'Lato'        => 'Lato',
            'Poppins'     => 'Poppins',
            'Raleway'     => 'Raleway',
            'Ubuntu'      => 'Ubuntu',
        ];
    }

    #[Computed]
    public function selectedBackgroundUrl(): ?string
    {
        return $this->selectedBackground?->getFirstMediaUrl('image');
    }

    #[Computed]
    public function platformOptions(): array
    {
        return [
            'instagram' => ['label' => 'Instagram', 'size' => '1080×1080'],
            'facebook'  => ['label' => 'Facebook',  'size' => '1200×630'],
            'linkedin'  => ['label' => 'LinkedIn',  'size' => '1200×627'],
            'story'     => ['label' => 'Story/Reels', 'size' => '1080×1920'],
        ];
    }

    #[Computed]
    public function patternOptions(): array
    {
        return [
            'dots'  => 'Pontos',
            'lines' => 'Linhas',
            'grid'  => 'Grade',
            'noise' => 'Ruído',
        ];
    }

    #[Computed]
    public function recentPosts(): \Illuminate\Database\Eloquent\Collection
    {
        return SocialPost::latest()->take(5)->get();
    }

    // ─── Actions ──────────────────────────────────────────────

    public function selectBackground(int $id): void
    {
        $this->backgroundImageId = ($this->backgroundImageId === $id) ? null : $id;
    }

    public function deleteBackground(int $id): void
    {
        $bg = BackgroundImage::find($id);
        
        if ($bg) {
            $bg->delete(); // Spatie Media Library handles file deletion automatically
            
            if ($this->backgroundImageId === $id) {
                $this->backgroundImageId = null;
            }

            Notification::make()
                ->title('Imagem removida!')
                ->success()
                ->send();
        }
    }

    public function selectPreset(string $value): void
    {
        $this->preset = $value;
        
        $presetEnum = \App\Enums\CardPreset::from($value);
        $style = $presetEnum->getStyle();
        
        $this->fontFamily = $style['font'];
        $this->textAlign = $style['align'] ?? 'center';
        $this->isBold = true; // Padrão
        $this->isItalic = false;
        
        if ($value === 'canva_side') {
            $this->textX = 24.0;
            $this->textY = 50.0;
        } else {
            $this->textX = 50.0;
            $this->textY = 50.0;
        }
    }

    public function updateCoordinates(float $x, float $y): void
    {
        $this->textX = $x;
        $this->textY = $y;
    }

    public function saveSnapshot(string $dataUrl): void
    {
        $this->validate([
            'quote'              => 'required|max:600',
            'backgroundImageId'  => 'required|exists:background_images,id',
        ], [
            'quote.required'             => 'Digite o texto/quote do post.',
            'backgroundImageId.required' => 'Selecione uma imagem de fundo.',
        ]);

        $post = SocialPost::create([
            'title'               => $this->postTitle ?: 'Arte ' . now()->format('H:i'),
            'platform'            => $this->platform,
            'quote'               => $this->quote,
            'font_family'         => $this->fontFamily,
            'font_size'           => $this->fontSize,
            'text_color'          => $this->textColor,
            'overlay_color'       => $this->overlayColor,
            'overlay_opacity'     => $this->overlayOpacity,
            'pattern'             => $this->pattern,
            'pattern_size'        => $this->patternSize,
            'pattern_color'       => $this->patternColor,
            'background_image_id' => $this->backgroundImageId,
            'preset'              => $this->preset,
            'status'              => 'processing',
        ]);

        // Novo Job que apenas salva o base64 como imagem final
        dispatch(new \App\Jobs\SavePostSnapshot($post, $dataUrl));

        Notification::make()
            ->title('Arte enviada para processamento!')
            ->success()
            ->send();
    }

    public function resetForm(): void
    {
        $this->postTitle        = '';
        $this->quote            = '';
        $this->backgroundImageId = null;
        $this->platform         = 'instagram';
        $this->preset           = 'bottom_right';
        $this->textX            = 50.0;
        $this->textY            = 50.0;
        $this->textAlign        = 'center';
        $this->isBold           = true;
        $this->isItalic         = false;
        $this->fontFamily       = 'Inter';
        $this->textColor        = '#ffffff';
        $this->fontSize         = 42;
        $this->overlayColor     = '#000000';
        $this->overlayOpacity   = 40;
        $this->pattern          = null;
        $this->patternSize     = 10;
        $this->patternColor    = '#ffffff';
    }

    public function updated($property)
    {
        $this->dispatch('updated');
    }



    public function regeneratePost(int $id): void
    {
        $post = SocialPost::find($id);
        
        if ($post) {
            $post->update(['status' => 'queued']);
            GenerateSocialPostImage::dispatch($post);
            
            Notification::make()
                ->title('Post reenviado para a fila!')
                ->success()
                ->send();
        }
    }

    protected function getActions(): array
    {
        return [
            $this->getUploadBackgroundAction(),
        ];
    }

    public function getUploadBackgroundAction(): Action
    {
        return Action::make('uploadBackground')
            ->label('Subir Imagem')
            ->icon('heroicon-o-plus')
            ->color('primary')
            ->modalHeading('Nova Imagem de Fundo')
            ->modalDescription('Suba uma foto para usar como fundo nos seus posts.')
            ->modalSubmitActionLabel('Salvar na Galeria')
            ->form([
                TextInput::make('name')
                    ->label('Nome da Imagem')
                    ->required()
                    ->placeholder('Ex: Paisagem de Verão'),
                FileUpload::make('image')
                    ->label('Arquivo')
                    ->image()
                    ->directory('backgrounds')
                    ->visibility('public')
                    ->required()
                    ->imageEditor(),
            ])
            ->action(function (array $data) {
                $bg = BackgroundImage::create([
                    'name'      => $data['name'],
                    'is_active' => true,
                ]);

                // Attach to media library using absolute path
                $bg->addMedia(storage_path('app/public/' . $data['image']))
                   ->toMediaCollection('image');

                // Selecionar automaticamente no editor
                $this->backgroundImageId = $bg->id;
                $this->updated('backgroundImageId');

                Notification::make()
                    ->title('Imagem salva e selecionada!')
                    ->success()
                    ->send();
            });
    }

    public function deletePost(int $id): void
    {
        $post = \App\Models\SocialPost::find($id);
        
        if ($post) {
            if ($post->output_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($post->output_path)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($post->output_path);
            }
            $post->delete();
            \Filament\Notifications\Notification::make()->title('Arte removida!')->success()->send();
        }
    }
}
