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
    public string $overlayColor = '#000000';
    public int    $overlayOpacity = 40;
    public string $preset         = 'bottom_right';
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
    public function recentPosts(): \Illuminate\Database\Eloquent\Collection
    {
        return SocialPost::latest()->take(5)->get();
    }

    // ─── Actions ──────────────────────────────────────────────

    public function selectBackground(int $id): void
    {
        $this->backgroundImageId = ($this->backgroundImageId === $id) ? null : $id;
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

    public function dispatchGeneration(): void
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
            'text_color'          => $this->textColor,
            'overlay_color'       => $this->overlayColor,
            'overlay_opacity'     => $this->overlayOpacity,
            'background_image_id' => $this->backgroundImageId,
            'preset'              => $this->preset,
            'text_x'              => $this->textX,
            'text_y'              => $this->textY,
            'text_align'          => $this->textAlign,
            'is_bold'             => $this->isBold,
            'is_italic'           => $this->isItalic,
            'status'              => 'queued',
        ]);

        GenerateSocialPostImage::dispatch($post);

        Notification::make()
            ->title('Post enviado para a fila!')
            ->body('A imagem será gerada em segundo plano. Você pode continuar trabalhando.')
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
        $this->overlayColor     = '#000000';
        $this->overlayOpacity   = 40;
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

    public function uploadBackgroundAction(): Action
    {
        return Action::make('uploadBackground')
            ->label('Subir Imagem')
            ->icon('heroicon-o-plus')
            ->color('amber')
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

                // Attach to media library
                $bg->addMediaFromDisk($data['image'])->toMediaCollection('image');

                Notification::make()
                    ->title('Imagem salva!')
                    ->success()
                    ->send();
            });
    }
}
