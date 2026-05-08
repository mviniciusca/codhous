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
    public array  $layers         = [];
    public ?string $logoUrl       = null;
    public ?string $frameUrl      = null;
    public bool   $hasVignette    = false;
    public string $vignetteType   = 'black';

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
        $this->isBold = true; 
        $this->isItalic = false;
        
        // Horizontal Alignment
        $align = $style['align'] ?? 'center';
        if ($align === 'left') {
            $this->textX = 25.0;
        } elseif ($align === 'right') {
            $this->textX = 75.0;
        } else {
            $this->textX = 50.0;
        }

        // Vertical Alignment
        $valign = $style['valign'] ?? 'center';
        if ($valign === 'top') {
            $this->textY = 20.0;
        } elseif ($valign === 'bottom') {
            $this->textY = 80.0;
        } else {
            $this->textY = 50.0;
        }

        if ($value === 'canva_side') {
            $this->textX = 24.0;
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
            'text_x'              => $this->textX,
            'text_y'              => $this->textY,
            'layers'              => $this->layers,
            'has_vignette'        => $this->hasVignette,
            'vignette_type'       => $this->vignetteType,
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

    public function processAIResponse(string $jsonFromAI): void
    {
        try {
            $data = json_decode($jsonFromAI, true);
            if (!$data) return;

            if (isset($data['canvas']['bg_color'])) {
                $this->overlayColor = $data['canvas']['bg_color'];
            }

            $this->layers = $data['layers'] ?? [];

            foreach ($this->layers as $layer) {
                if ($layer['type'] === 'text') {
                    $this->quote = $layer['content'];
                    if (isset($layer['style']['color'])) $this->textColor = $layer['style']['color'];
                    if (isset($layer['style']['size'])) $this->fontSize = (int) str_replace('px', '', $layer['style']['size']);
                    if (isset($layer['style']['font'])) $this->fontFamily = $layer['style']['font'];
                    if (isset($layer['style']['weight'])) $this->isBold = $layer['style']['weight'] === 'bold' || $layer['style']['weight'] === '900';
                    
                    if (isset($layer['position']['top'])) $this->textY = (float) str_replace('%', '', $layer['position']['top']);
                    if (isset($layer['position']['left'])) $this->textX = (float) str_replace('%', '', $layer['position']['left']);
                    if (isset($layer['position']['align'])) $this->textAlign = $layer['position']['align'];
                }

                if ($layer['type'] === 'frame') {
                    // Logic to select a frame preset or URL
                    // For now just storing it in the state
                    $this->frameUrl = $layer['url'] ?? null;
                }

                if ($layer['type'] === 'branding') {
                    $this->logoUrl = $layer['url'] ?? null;
                }

                if ($layer['type'] === 'vignette') {
                    $this->hasVignette = true;
                    $this->vignetteType = ($layer['style']['color'] ?? '') === '#FFFFFF' ? 'white' : 'black';
                }
            }

            $this->dispatch('updated');
            
            Notification::make()
                ->title('Design sugerido pela IA aplicado!')
                ->success()
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->title('Erro ao processar sugestão da IA')
                ->danger()
                ->send();
        }
    }

    public function generateAiDesign(): void
    {
        if (empty($this->quote)) {
            Notification::make()->title('Digite uma frase primeiro!')->warning()->send();
            return;
        }

        // Simulação do JSON que a IA retornaria
        // Em um cenário real, você faria uma chamada para a API da OpenAI/Anthropic/Gemini aqui
        $mockJson = json_encode([
            "canvas" => [ "width" => 1080, "height" => 1080, "bg_color" => "#1a1a1a" ],
            "layers" => [
                [
                    "type" => "text",
                    "content" => strtoupper($this->quote),
                    "style" => [ "color" => "#fbbf24", "size" => "64px", "font" => "Oswald", "weight" => "bold" ],
                    "position" => [ "top" => "50%", "left" => "50%", "align" => "center" ],
                    "z_index" => 30
                ],
                [
                    "type" => "frame",
                    "url" => "https://www.transparentpng.com/download/border/gold-square-border-free-png-2775.png",
                    "opacity" => 0.8,
                    "z_index" => 10
                ]
            ]
        ]);

        $this->processAIResponse($mockJson);
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
            $post->delete(); // Spatie Media Library handles media deletion
            \Filament\Notifications\Notification::make()
                ->title('Arte removida!')
                ->success()
                ->send();
        }
    }
}
