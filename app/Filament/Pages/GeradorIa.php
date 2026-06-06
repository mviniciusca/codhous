<?php

namespace App\Filament\Pages;

use App\Models\BackgroundImage;
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

use Livewire\WithFileUploads;

class GeradorIa extends Page implements HasActions, HasForms
{
    use InteractsWithActions, InteractsWithForms, WithFileUploads;
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
    public $logoUpload;
    public $frameUpload;
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
        
        // Mantém a fonte escolhida pelo usuário em vez de sobrescrever com a do preset
        // $this->fontFamily = $style['font'];
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

            // Reseta opções secundárias para aplicar o novo design randômico completamente
            $this->frameUrl = null;
            $this->hasVignette = false;
            $this->pattern = null;

            if (isset($data['canvas']['bg_color'])) {
                $this->overlayColor = $data['canvas']['bg_color'];
            }
            if (isset($data['canvas']['bg_opacity'])) {
                $this->overlayOpacity = (int) $data['canvas']['bg_opacity'];
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
                    $this->frameUrl = $layer['url'] ?? null;
                }

                if ($layer['type'] === 'branding') {
                    $this->logoUrl = $layer['url'] ?? null;
                }

                if ($layer['type'] === 'vignette') {
                    $this->hasVignette = true;
                    $this->vignetteType = ($layer['style']['color'] ?? '') === '#FFFFFF' ? 'white' : 'black';
                }

                if ($layer['type'] === 'pattern') {
                    $this->pattern = $layer['name'] ?? null;
                    if (isset($layer['style']['size'])) $this->patternSize = (int) $layer['style']['size'];
                    if (isset($layer['style']['color'])) $this->patternColor = $layer['style']['color'];
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

        // Paleta de cores premium (escuros, vibrantes e neutros)
        $bgColors = [
            '#121217', '#09090b', '#1a1a1a', '#1e1e24', '#2d3748', '#1a202c', '#0f172a', '#172554', '#3b0764',
            '#3b82f6', '#ef4444', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899', '#06b6d4', '#14b8a6',
            '#f8fafc', '#f1f5f9', '#fafafa', '#fdf2f8', '#ecfdf5', '#eff6ff',
        ];
        $bgColor = $bgColors[array_rand($bgColors)];

        // Verifica a luminosidade da cor de fundo para garantir o contraste do texto
        $hex = ltrim($bgColor, '#');
        [$r, $g, $b] = sscanf($hex, '%02x%02x%02x');
        $brightness = ($r * 299 + $g * 587 + $b * 114) / 1000;
        $isBgLight = $brightness > 127;

        if ($isBgLight) {
            $textColors = ['#1e293b', '#0f172a', '#000000', '#312e81', '#581c87', '#7c2d12', '#064e3b'];
        } else {
            $textColors = ['#ffffff', '#fbbf24', '#38bdf8', '#34d399', '#f472b6', '#a78bfa', '#fda4af'];
        }
        $textColor = $textColors[array_rand($textColors)];

        // Escolhe uma fonte aleatória
        $fonts = array_keys($this->fontOptions);
        $font = $fonts[array_rand($fonts)];

        $size = rand(36, 76) . 'px';
        $weight = (rand(0, 1) === 1) ? 'bold' : 'normal';

        // Escolhe alinhamento e calcula posições correspondentes
        $aligns = ['left', 'center', 'right'];
        $align = $aligns[array_rand($aligns)];

        $x = '50%';
        if ($align === 'left') {
            $x = rand(0, 1) ? '25%' : '20%';
        } elseif ($align === 'right') {
            $x = rand(0, 1) ? '75%' : '80%';
        }

        $yOptions = ['25%', '50%', '75%'];
        $y = $yOptions[array_rand($yOptions)];

        $opacity = rand(20, 80);

        // Montagem das camadas dinâmicas da IA
        $layers = [];

        // Camada de Texto
        $layers[] = [
            "type" => "text",
            "content" => strtoupper($this->quote),
            "style" => [
                "color" => $textColor,
                "size" => $size,
                "font" => $font,
                "weight" => $weight,
            ],
            "position" => [
                "top" => $y,
                "left" => $x,
                "align" => $align,
            ],
            "z_index" => 30
        ];

        // Camada de Vinheta (40% de chance)
        if (rand(1, 100) <= 40) {
            $vignetteColor = $isBgLight ? '#FFFFFF' : '#000000';
            $layers[] = [
                "type" => "vignette",
                "style" => [
                    "color" => $vignetteColor,
                ],
                "z_index" => 7
            ];
        }

        // Camada de Textura/Pattern (35% de chance)
        if (rand(1, 100) <= 35) {
            $patterns = ['dots', 'lines', 'grid'];
            $patternName = $patterns[array_rand($patterns)];
            $patternColor = $isBgLight ? '#000000' : '#ffffff';
            $patternSize = rand(8, 22);

            $layers[] = [
                "type" => "pattern",
                "name" => $patternName,
                "style" => [
                    "color" => $patternColor,
                    "size" => $patternSize,
                ],
                "z_index" => 6
            ];
        }

        // Camada de Moldura (20% de chance)
        if (rand(1, 100) <= 20) {
            $layers[] = [
                "type" => "frame",
                "url" => "https://www.transparentpng.com/download/border/gold-square-border-free-png-2775.png",
                "opacity" => 0.8,
                "z_index" => 10
            ];
        }

        $mockJson = json_encode([
            "canvas" => [ 
                "width" => 1080, 
                "height" => 1080, 
                "bg_color" => $bgColor,
                "bg_opacity" => $opacity
            ],
            "layers" => $layers
        ]);

        $this->processAIResponse($mockJson);
    }

    public function updated($property)
    {
        $this->dispatch('updated');

        if ($property === 'logoUpload' && $this->logoUpload) {
            $this->logoUrl = $this->logoUpload->temporaryUrl();
        }

        if ($property === 'frameUpload' && $this->frameUpload) {
            $this->frameUrl = $this->frameUpload->temporaryUrl();
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
}
