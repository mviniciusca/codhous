<?php

namespace App\Filament\Pages;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class PosterBuilder extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static string $view = 'filament.pages.poster-builder';

    protected static ?int $navigationSort = 3;
    protected static ?string $title = 'Gerador de Posters';
    protected ?string $subheading = 'Gere e baixe artes personalizadas com a sua logo e telefone para postar nas redes sociais, enviar por WhatsApp, etc.';

    public ?array $data = [];
    public ?string $generatedImageUrl = null;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Configuração do Poster')
                    ->schema([
                        \Filament\Forms\Components\ViewField::make('template')
                            ->view('filament.forms.components.image-radio')
                            ->required()
                            ->columnSpanFull()
                            ->label('Template de Fundo')
                            ->helperText('Adicione imagens na pasta public/img/templates para que apareçam aqui.'),
                    ])->columns(1)
            ])
            ->statePath('data');
    }

    public function generate()
    {
        $data = $this->form->getState();

        $templatePath = public_path('img/templates/' . $data['template']);
        
        $settings = \App\Models\Setting::first()->settings ?? [];
        $logoPath = $settings['website']['logo'] ?? $settings['layout']['logo'] ?? null;
        $phone = $settings['company']['phone'] ?? '';

        // Instancia o Intervention Image Manager (versão 3)
        $manager = new ImageManager(new Driver());

        if (!$logoPath || !Storage::disk('public')->exists($logoPath)) {
            \Filament\Notifications\Notification::make()
                ->title('Aviso: Logo não encontrada')
                ->body('O poster será gerado sem a logo da empresa. Configure a logo em Configurações.')
                ->warning()
                ->send();
        }

        if (!$phone) {
            \Filament\Notifications\Notification::make()
                ->title('Aviso: Telefone não encontrado')
                ->body('O poster será gerado sem o número de telefone. Configure o telefone em Configurações.')
                ->warning()
                ->send();
        }

        try {
            // Carrega o template
            $image = $manager->decodePath($templatePath);

            // Carrega a logo
            if ($logoPath && Storage::disk('public')->exists($logoPath)) {
                $logo = $manager->decodePath(Storage::disk('public')->path($logoPath));
                
                // Redimensiona a logo para não ficar gigante (ex: máx 300px largura)
                $logo->scaleDown(width: 300);

                // Insere a logo na imagem (ex: topo-esquerda com margem)
                $image->insert($logo, 50, 50, 'top-left');
            }

            // Adiciona o telefone
            if ($phone) {
                $image->text($phone, 470, 1150, function ($font) {
                    $font->file(base_path('vendor/dompdf/dompdf/lib/fonts/DejaVuSans-Bold.ttf'));
                    $font->size(54);
                    $font->color('#ea580c');
                    $font->align('center', 'center');
                });
            }

            // Busca o mascote nas configurações globais
            $mascotPath = $settings['website']['mascot'] ?? null;

            if ($mascotPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($mascotPath)) {
                $mascotAbsPath = \Illuminate\Support\Facades\Storage::disk('public')->path($mascotPath);
                $mascotImage = $manager->decodePath($mascotAbsPath);
                
                // Redimensiona o mascote para caber bem no rodapé (ex: máx 500px de altura)
                $mascotImage->scaleDown(height: 500);
                
                // Insere o mascote no canto inferior direito
                $image->insert($mascotImage, -50, -50, 'bottom-right');
            }

            // Cria o nome do arquivo final
            $filename = 'posters/generated_' . time() . '.jpg';
            
            // Garante que o diretório exista
            if (!Storage::disk('public')->exists('posters')) {
                Storage::disk('public')->makeDirectory('posters');
            }

            // Salva a imagem
            $image->save(Storage::disk('public')->path($filename));

            // Define a URL para exibir no Blade
            $this->generatedImageUrl = Storage::url($filename);



        } catch (\Exception $e) {
            // Em caso de erro
            \Filament\Notifications\Notification::make()
                ->title('Erro ao gerar poster')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
