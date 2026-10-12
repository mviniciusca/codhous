<?php

namespace App\Filament\Pages;

use App\Filament\Forms\Components\ColorSwatches;
use App\Http\Controllers\DesignPreviewController;
use App\Models\Page as SitePage;
use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\View;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Renderless;

/**
 * Identidade, SEO, navegação, atendimento e scripts do site, com prévia ao vivo
 * do site real ao lado (DesignPreviewController).
 */
class DesignAndSeo extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-paint-brush';

    protected static ?string $navigationGroup = 'Website';

    protected static ?string $navigationLabel = 'Design e SEO';

    protected static ?string $title = 'Design e SEO';

    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.pages.design-and-seo';

    /** Campos de imagem: aparecem na prévia antes de salvar */
    protected const IMAGE_FIELDS = ['logo', 'favicon', 'mascot'];

    /** Cores de destaque disponíveis para o site ("R G B" => nome) */
    public const COLORS = [
        '239 68 68' => 'Vermelho',
        '249 115 22' => 'Laranja',
        '245 158 11' => 'Âmbar',
        '234 179 8' => 'Amarelo',
        '132 204 22' => 'Lima',
        '34 197 94' => 'Verde',
        '16 185 129' => 'Esmeralda',
        '20 184 166' => 'Teal',
        '6 182 212' => 'Ciano',
        '14 165 233' => 'Sky',
        '59 130 246' => 'Azul',
        '99 102 241' => 'Índigo',
        '139 92 246' => 'Violeta',
        '168 85 247' => 'Roxo',
        '192 38 211' => 'Fúcsia',
        '236 72 153' => 'Pink',
        '244 63 94' => 'Rose',
        '113 113 122' => 'Zinco (Cinza)',
    ];

    public ?array $data = [];

    public function mount(): void
    {
        $this->fillFromDatabase();
    }

    public function getSubheading(): ?string
    {
        return 'Identidade, busca, menu e atendimento do site, com prévia ao vivo.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('discard')
                ->label('Descartar')
                ->color('gray')
                ->icon('heroicon-o-arrow-uturn-left')
                ->requiresConfirmation()
                ->modalHeading('Descartar alterações?')
                ->modalDescription('O formulário volta para a última versão salva.')
                ->modalSubmitActionLabel('Descartar')
                ->action('discard')
                // Só aparece com alterações pendentes (a view marca <html class="ds-dirty">)
                ->extraAttributes(['data-ds-discard' => true]),
            Action::make('save')
                ->label('Salvar alterações')
                ->icon('heroicon-o-check')
                ->keyBindings(['mod+s'])
                ->action('save'),
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('design')
                    ->id('ds')
                    ->contained(false)
                    ->persistTabInQueryString('aba')
                    ->tabs([
                        Tab::make('Identidade')
                            ->id('identidade')
                            ->icon('heroicon-o-swatch')
                            ->schema([
                                Section::make('Identidade visual')
                                    ->description('Cor e imagens da marca no site.')
                                    ->compact()
                                    ->schema([
                                        ColorSwatches::make('settings.website.primary_color')
                                            ->label('Cor de destaque')
                                            ->helperText('Usada em botões, links e destaques do site.')
                                            ->options(self::COLORS)
                                            ->default('249 115 22'),

                                        FileUpload::make('settings.website.logo')
                                            ->label('Logotipo')
                                            ->helperText('SVG ou PNG transparente, até 2 MB.')
                                            ->image()
                                            ->imageEditor()
                                            ->imagePreviewHeight('96')
                                            ->directory('website')
                                            ->visibility('public'),

                                        Grid::make(['default' => 1, 'sm' => 2])->schema([
                                            FileUpload::make('settings.website.favicon')
                                                ->label('Favicon')
                                                ->helperText('PNG ou ICO, 32×32 ou 48×48 px.')
                                                ->image()
                                                ->imagePreviewHeight('72')
                                                ->directory('website')
                                                ->visibility('public'),
                                            FileUpload::make('settings.website.mascot')
                                                ->label('Mascote')
                                                ->helperText('PNG transparente, usado no Estúdio de Posts.')
                                                ->image()
                                                ->imagePreviewHeight('72')
                                                ->directory('website')
                                                ->visibility('public'),
                                        ]),
                                    ]),

                                Section::make('Busca no Google')
                                    ->description('Como o site aparece nos resultados de busca e na aba do navegador.')
                                    ->compact()
                                    ->schema([
                                        TextInput::make('settings.website.name')
                                            ->label('Nome do site')
                                            ->required()
                                            ->maxLength(255),
                                        TextInput::make('settings.website.title')
                                            ->label('Título (SEO)')
                                            ->placeholder('Ex: Concreto usinado no Rio de Janeiro')
                                            ->helperText('Aparece depois do nome na aba do navegador. Ideal: até 60 caracteres.')
                                            ->maxLength(255),
                                        Textarea::make('settings.website.description')
                                            ->label('Descrição (SEO)')
                                            ->helperText('Resumo exibido abaixo do título no Google. Ideal: até 160 caracteres.')
                                            ->rows(3),
                                        View::make('filament.design.search-preview'),
                                    ]),
                            ]),

                        Tab::make('Navegação')
                            ->id('navegacao')
                            ->icon('heroicon-o-bars-3')
                            ->schema([
                                Section::make('Menu principal')
                                    ->description('Links do topo do site. Arraste para reordenar.')
                                    ->compact()
                                    ->schema([
                                        Toggle::make('settings.website.sticky_menu')
                                            ->label('Menu fixo ao rolar a página')
                                            ->default(true),
                                        Repeater::make('settings.website.navigation')
                                            ->hiddenLabel()
                                            ->addActionLabel('Adicionar link')
                                            ->collapsible()
                                            ->collapsed()
                                            ->cloneable()
                                            ->schema([
                                                TextInput::make('label')
                                                    ->label('Texto')
                                                    ->placeholder('Ex: Início')
                                                    ->required(),
                                                TextInput::make('url')
                                                    ->label('Destino')
                                                    ->placeholder('Ex: /servicos')
                                                    ->required(),
                                            ])
                                            ->columns(2)
                                            ->itemLabel(fn (array $state): ?string => filled($state['label'] ?? null)
                                                ? $state['label'].(filled($state['url'] ?? null) ? '  ·  '.$state['url'] : '')
                                                : 'Novo link'),
                                    ]),

                                Section::make('Redes sociais')
                                    ->description('Exibidas no rodapé do site.')
                                    ->compact()
                                    ->schema([
                                        Grid::make(['default' => 1, 'md' => 2])->schema([
                                            TextInput::make('settings.website.social_networks.instagram')
                                                ->label('Instagram')
                                                ->prefix('instagr.am/'),
                                            TextInput::make('settings.website.social_networks.facebook')
                                                ->label('Facebook')
                                                ->prefix('fb.com/'),
                                            TextInput::make('settings.website.social_networks.linkedin')
                                                ->label('LinkedIn')
                                                ->prefix('linkedin.com/in/'),
                                            TextInput::make('settings.website.social_networks.twitter')
                                                ->label('X (Twitter)')
                                                ->prefix('x.com/'),
                                        ]),
                                        TextInput::make('settings.website.social_networks.whatsapp')
                                            ->label('Link direto do WhatsApp')
                                            ->placeholder('https://wa.me/55...'),
                                    ]),
                            ]),

                        Tab::make('Atendimento')
                            ->id('atendimento')
                            ->icon('heroicon-o-chat-bubble-left-right')
                            ->schema([
                                Section::make('WhatsApp')
                                    ->description('Número oficial usado no botão flutuante e na página de contato.')
                                    ->compact()
                                    ->schema([
                                        TextInput::make('settings.website.features.whatsapp_widget.number')
                                            ->label('Número')
                                            ->prefix('+55')
                                            ->mask('(99) 99999-9999')
                                            ->placeholder('(21) 90000-0000')
                                            ->required()
                                            ->tel(),
                                        Toggle::make('settings.website.features.whatsapp_widget.enabled')
                                            ->label('Botão flutuante em todas as páginas')
                                            ->live(),
                                        TextInput::make('settings.website.features.whatsapp_widget.message')
                                            ->label('Mensagem inicial')
                                            ->placeholder('Olá! Gostaria de um orçamento.')
                                            ->required(fn (Get $get) => $get('settings.website.features.whatsapp_widget.enabled'))
                                            ->visible(fn (Get $get) => $get('settings.website.features.whatsapp_widget.enabled')),
                                        Toggle::make('settings.website.features.whatsapp_button.enabled')
                                            ->label('Botão no formulário de contato')
                                            ->live(),
                                        TextInput::make('settings.website.features.whatsapp_button.text')
                                            ->label('Texto do botão')
                                            ->default('Chamar no WhatsApp')
                                            ->placeholder('Ex: Falar com um especialista')
                                            ->required(fn (Get $get) => $get('settings.website.features.whatsapp_button.enabled'))
                                            ->visible(fn (Get $get) => $get('settings.website.features.whatsapp_button.enabled')),
                                    ]),

                                Section::make('Ferramentas do site')
                                    ->compact()
                                    ->schema([
                                        Toggle::make('settings.website.features.budget_tool')
                                            ->label('Solicitação de orçamento online'),
                                        Toggle::make('settings.website.features.concrete_calculator')
                                            ->label('Calculadora de volume de concreto'),
                                        Toggle::make('settings.website.features.newsletter')
                                            ->label('Inscrição na newsletter (rodapé)')
                                            ->default(true),
                                        Toggle::make('settings.website.features.scroll_to_top')
                                            ->label('Botão “voltar ao topo”')
                                            ->default(true),
                                    ]),
                            ]),

                        Tab::make('Integrações')
                            ->id('integracoes')
                            ->icon('heroicon-o-code-bracket')
                            ->schema([
                                Section::make('Fonte dos títulos')
                                    ->compact()
                                    ->schema([
                                        TextInput::make('settings.website.scripts.google_font_family')
                                            ->label('Google Fonts')
                                            ->placeholder('Ex: Outfit')
                                            ->helperText('Só o nome da família, como aparece no Google Fonts.'),
                                    ]),
                                Section::make('Scripts')
                                    ->description('Analytics, Tag Manager, Pixel. Não rodam na prévia, só no site publicado.')
                                    ->compact()
                                    ->schema([
                                        Textarea::make('settings.website.scripts.head_scripts')
                                            ->label('No <head>')
                                            ->rows(5)
                                            ->extraInputAttributes(['class' => 'font-mono text-xs', 'spellcheck' => 'false']),
                                        Textarea::make('settings.website.scripts.footer_scripts')
                                            ->label('Antes de </body>')
                                            ->rows(5)
                                            ->extraInputAttributes(['class' => 'font-mono text-xs', 'spellcheck' => 'false']),
                                    ]),
                            ]),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $setting = Setting::firstOrCreate(['id' => 1]);

        $settings = Setting::merge($setting->settings ?? [], $data['settings'] ?? []);

        // Chaves antigas, gravadas antes de alinhar o formulário com o que o site lê
        unset($settings['website']['scripts']['head'], $settings['website']['scripts']['footer']);

        $setting->update(['settings' => $settings]);

        $this->dispatch('design-saved');

        Notification::make()
            ->success()
            ->title('Configurações salvas')
            ->send();
    }

    public function discard(): void
    {
        $this->fillFromDatabase();
        $this->dispatch('design-saved');

        Notification::make()->title('Alterações descartadas')->send();
    }

    /**
     * Guarda o estado atual (sem salvar) e devolve a URL da prévia.
     */
    #[Renderless]
    public function previewUrl(string $path = '/'): string
    {
        $saved = Setting::firstOrCreate(['id' => 1])->settings ?? [];
        $draft = $this->normalizeDraft(data_get($this->data, 'settings', []));

        $token = hash_hmac('sha256', session()->getId().'|'.auth()->id().'|design', (string) config('app.key'));

        Cache::put(DesignPreviewController::CACHE_PREFIX.$token, [
            'user_id' => (string) auth()->id(),
            'settings' => Setting::merge($saved, $draft),
        ], now()->addMinutes(DesignPreviewController::TTL_MINUTES));

        return route('design.preview', ['token' => $token, 'pagina' => $path, 'v' => now()->getTimestampMs()]);
    }

    /**
     * @return array<int, array{title: string, path: string}>
     */
    public function getPreviewPages(): array
    {
        return SitePage::query()
            ->visible()
            ->orderByRaw("CASE WHEN slug = '/' THEN 0 ELSE 1 END")
            ->orderBy('sort_order')
            ->get(['title', 'slug'])
            ->map(fn (SitePage $page) => [
                'title' => $page->title,
                'path' => $page->slug === '/' ? '/' : '/'.ltrim($page->slug, '/'),
            ])
            ->all();
    }

    protected function fillFromDatabase(): void
    {
        $settings = Setting::firstOrCreate(['id' => 1])->settings ?? [];

        // Leva valores das chaves antigas para as que o site realmente lê
        foreach (['head', 'footer'] as $old) {
            $new = "{$old}_scripts";
            if (blank(data_get($settings, "website.scripts.{$new}")) && filled(data_get($settings, "website.scripts.{$old}"))) {
                data_set($settings, "website.scripts.{$new}", data_get($settings, "website.scripts.{$old}"));
            }
        }

        $this->form->fill(['settings' => $settings]);
    }

    /**
     * Estado bruto do formulário no formato salvo: uploads viram caminhos no disco público.
     */
    protected function normalizeDraft(array $settings): array
    {
        foreach (self::IMAGE_FIELDS as $field) {
            $state = data_get($settings, "website.{$field}");
            if (! is_array($state)) {
                continue;
            }

            $file = collect($state)->filter()->first();

            if ($file instanceof UploadedFile) {
                $file = $this->storePreviewImage($file, $field);
            }

            data_set($settings, "website.{$field}", is_string($file) ? $file : null);
        }

        return $this->dropObjects($settings);
    }

    /**
     * Upload ainda não salvo: copia para um caminho fixo por usuário e campo
     * (sobrescrito a cada prévia), servido pelo disco público como os demais.
     */
    protected function storePreviewImage(UploadedFile $file, string $field): ?string
    {
        try {
            $path = 'website/preview/'.auth()->id().'-'.$field.'.'.($file->guessExtension() ?: 'png');
            Storage::disk('public')->put($path, $file->get());

            return $path;
        } catch (\Throwable $e) {
            report($e);

            return null;
        }
    }

    protected function dropObjects(array $value): array
    {
        return array_map(fn ($item) => match (true) {
            is_array($item) => $this->dropObjects($item),
            is_object($item) => null,
            default => $item,
        }, $value);
    }
}
