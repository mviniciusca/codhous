<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;

class DesignAndSeo extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';

    protected static ?string $navigationGroup = 'Website';

    protected static ?string $navigationLabel = 'Design e SEO';

    protected static ?string $title = 'Design e SEO';

    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.pages.design-and-seo';

    public ?array $data = [];

    public function mount(): void
    {
        $setting = Setting::firstOrCreate(['id' => 1]);
        $this->form->fill([
            'settings' => $setting->settings,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Identidade Visual')
                    ->icon('heroicon-o-swatch')
                    ->description('Defina a cor principal para todo o site.')
                    ->schema([
                        \Filament\Forms\Components\ToggleButtons::make('settings.website.primary_color')
                            ->label('Cor de Destaque (Branding)')
                            ->options([
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
                            ])
                            ->colors([
                                '239 68 68' => \Filament\Support\Colors\Color::Red,
                                '249 115 22' => \Filament\Support\Colors\Color::Orange,
                                '245 158 11' => \Filament\Support\Colors\Color::Amber,
                                '234 179 8' => \Filament\Support\Colors\Color::Yellow,
                                '132 204 22' => \Filament\Support\Colors\Color::Lime,
                                '34 197 94' => \Filament\Support\Colors\Color::Green,
                                '16 185 129' => \Filament\Support\Colors\Color::Emerald,
                                '20 184 166' => \Filament\Support\Colors\Color::Teal,
                                '6 182 212' => \Filament\Support\Colors\Color::Cyan,
                                '14 165 233' => \Filament\Support\Colors\Color::Sky,
                                '59 130 246' => \Filament\Support\Colors\Color::Blue,
                                '99 102 241' => \Filament\Support\Colors\Color::Indigo,
                                '139 92 246' => \Filament\Support\Colors\Color::Violet,
                                '168 85 247' => \Filament\Support\Colors\Color::Purple,
                                '192 38 211' => \Filament\Support\Colors\Color::Fuchsia,
                                '236 72 153' => \Filament\Support\Colors\Color::Pink,
                                '244 63 94' => \Filament\Support\Colors\Color::Rose,
                                '113 113 122' => \Filament\Support\Colors\Color::Zinc,
                            ])
                            ->inline()
                            ->default('249 115 22')
                            ->helperText('Escolha a cor principal da identidade visual da sua loja.'),
                    ]),

                Section::make('Identidade Visual')
                    ->icon('heroicon-o-photo')
                    ->description('Gerencie o logotipo da sua empresa.')
                    ->schema([
                        FileUpload::make('settings.website.logo')
                            ->label('Logotipo')
                            ->helperText('Recomendado: SVG ou PNG transparente. Tamanho máx: 2MB.')
                            ->image()
                            ->imageEditor()
                            ->directory('website')
                            ->visibility('public'),
                        FileUpload::make('settings.website.favicon')
                            ->label('Favicon')
                            ->helperText('Recomendado: PNG ou ICO (32x32px ou 48x48px).')
                            ->image()
                            ->directory('website')
                            ->visibility('public'),
                        FileUpload::make('settings.website.mascot')
                            ->label('Mascote da Empresa')
                            ->helperText('Imagem transparente (PNG) do mascote. Usado na geração de artes.')
                            ->image()
                            ->directory('website')
                            ->visibility('public'),
                    ]),



                Section::make('Identidade e SEO')
                    ->icon('heroicon-o-information-circle')
                    ->description('Configure as informações básicas e metatags para motores de busca.')
                    ->schema([
                        TextInput::make('settings.website.name')
                            ->label('Nome do Website')
                            ->helperText('O nome oficial do seu site.')
                            ->required(),
                        TextInput::make('settings.website.title')
                            ->label('Título (SEO)')
                            ->helperText('Título que aparece na aba do navegador e no Google.')
                            ->placeholder('Ex: Codhous - Software Sob Medida'),
                        Textarea::make('settings.website.description')
                            ->label('Descrição (SEO)')
                            ->helperText('Breve resumo do site para os resultados de busca.')
                            ->rows(3),
                    ]),

                Section::make('Navegação e Menus')
                    ->icon('heroicon-o-bars-3')
                    ->description('Gerencie os links que compõem o menu principal do site.')
                    ->schema([
                        Toggle::make('settings.website.sticky_menu')
                            ->label('Menu Fixo (Sticky)')
                            ->helperText('O menu principal (barra branca com logo e links) ficará fixo no topo ao rolar a página.')
                            ->default(true)
                            ->onIcon('heroicon-m-check'),
                        Repeater::make('settings.website.navigation')
                            ->label('Links do Menu')
                            ->helperText('Adicione ou remova itens do menu superior.')
                            ->collapsible()
                            ->collapsed()
                            ->cloneable()
                            ->collapseAllAction(fn (\Filament\Forms\Components\Actions\Action $action) => $action->label('Recolher Tudo'))
                            ->expandAllAction(fn (\Filament\Forms\Components\Actions\Action $action) => $action->label('Expandir Tudo'))
                            ->schema([
                                TextInput::make('label')
                                    ->label('Rótulo')
                                    ->placeholder('Ex: Início')
                                    ->required(),
                                TextInput::make('url')
                                    ->label('URL/Rota')
                                    ->placeholder('Ex: /produtos')
                                    ->required(),
                            ])
                            ->columns(2)
                            ->itemLabel(fn(array $state): ?string => $state['label'] ?? null),
                    ]),

                Section::make('Ferramentas Ativas')
                    ->icon('heroicon-o-cpu-chip')
                    ->description('Habilite ou desabilite módulos específicos do seu website.')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Toggle::make('settings.website.features.concrete_calculator')
                                    ->label('Calculadora de Concreto')
                                    ->helperText('Exibe a ferramenta de cálculo de volume na frente do site.')
                                    ->onIcon('heroicon-m-check')
                                    ->inline(false),
                                Toggle::make('settings.website.features.budget_tool')
                                    ->label('Ferramenta de Orçamento')
                                    ->helperText('Permite que os clientes solicitem orçamentos online.')
                                    ->onIcon('heroicon-m-check')
                                    ->inline(false),
                                Toggle::make('settings.website.features.scroll_to_top')
                                    ->label('Botão Voltar ao Topo')
                                    ->helperText('Exibe um botão flutuante para subir a página rapidamente.')
                                    ->onIcon('heroicon-m-check')
                                    ->default(true)
                                    ->inline(false),
                                Toggle::make('settings.website.features.newsletter')
                                    ->label('Formulário de Newsletter')
                                    ->helperText('Exibe um formulário de inscrição na newsletter no rodapé do site.')
                                    ->onIcon('heroicon-m-check')
                                    ->default(true)
                                    ->inline(false),
                            ]),
                    ]),

                Section::make('Atendimento via WhatsApp')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->description('Configurações do WhatsApp para o widget flutuante e botões de contato.')
                    ->schema([
                        TextInput::make('settings.website.features.whatsapp_widget.number')
                            ->label('Número do WhatsApp Oficial')
                            ->prefix('+55')
                            ->mask('(99) 99999-9999')
                            ->placeholder('(21) 90000-0000')
                            ->helperText('O código +55 já está incluído. Usado tanto para o widget quanto para os botões.')
                            ->required()
                            ->tel(),
                            
                        \Filament\Forms\Components\Fieldset::make('Widget Flutuante')
                            ->schema([
                                Toggle::make('settings.website.features.whatsapp_widget.enabled')
                                    ->label('Habilitar Widget')
                                    ->helperText('Ativa o botão flutuante no canto da tela em todas as páginas.')
                                    ->onIcon('heroicon-m-check')
                                    ->reactive()
                                    ->columnSpanFull(),
                                TextInput::make('settings.website.features.whatsapp_widget.message')
                                    ->label('Mensagem Inicial do Widget')
                                    ->helperText('Texto pré-preenchido para o cliente no widget flutuante.')
                                    ->placeholder('Olá! Gostaria de um orçamento.')
                                    ->required(fn($get) => $get('settings.website.features.whatsapp_widget.enabled'))
                                    ->visible(fn($get) => $get('settings.website.features.whatsapp_widget.enabled'))
                                    ->columnSpanFull(),
                            ]),

                        \Filament\Forms\Components\Fieldset::make('Botão na Página de Contato')
                            ->schema([
                                Toggle::make('settings.website.features.whatsapp_button.enabled')
                                    ->label('Habilitar Botão de Contato')
                                    ->helperText('Adiciona um botão chamativo de WhatsApp acima do e-mail no formulário de contato.')
                                    ->onIcon('heroicon-m-check')
                                    ->reactive()
                                    ->columnSpanFull(),
                                TextInput::make('settings.website.features.whatsapp_button.text')
                                    ->label('Texto do Botão')
                                    ->default('Chamar no WhatsApp')
                                    ->placeholder('Ex: Falar com Especialista')
                                    ->required(fn($get) => $get('settings.website.features.whatsapp_button.enabled'))
                                    ->visible(fn($get) => $get('settings.website.features.whatsapp_button.enabled'))
                                    ->columnSpanFull(),
                            ]),
                    ]),


                Section::make('Redes Sociais')
                    ->icon('heroicon-o-share')
                    ->description('Links para as redes sociais da sua empresa.')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('settings.website.social_networks.instagram')
                                    ->label('Instagram')
                                    ->helperText('URL completa ou apenas o @usuario.')
                                    ->prefix('instagr.am/'),
                                TextInput::make('settings.website.social_networks.facebook')
                                    ->label('Facebook')
                                    ->helperText('Link da sua página no Facebook.')
                                    ->prefix('fb.com/'),
                                TextInput::make('settings.website.social_networks.linkedin')
                                    ->label('LinkedIn')
                                    ->helperText('Link do perfil profissional ou empresa.')
                                    ->prefix('linkedin.com/in/'),
                                TextInput::make('settings.website.social_networks.twitter')
                                    ->label('Twitter / X')
                                    ->helperText('Link do seu perfil no Twitter/X.')
                                    ->prefix('x.com/'),
                                TextInput::make('settings.website.social_networks.whatsapp')
                                    ->label('Link Direto WhatsApp')
                                    ->helperText('URL gerada (ex: wa.me/...).')
                                    ->placeholder('https://wa.me/55...'),
                            ]),
                    ]),

                Section::make('Integrações e Scripts')
                    ->icon('heroicon-o-code-bracket')
                    ->description('Insira códigos de rastreamento, fontes ou scripts externos.')
                    ->schema([
                        TextInput::make('settings.website.scripts.google_font_family')
                            ->label('Nome da Fonte (Google Fonts)')
                            ->helperText('Digite apenas o nome da fonte. Ex: Montserrat, Poppins, Outfit.')
                            ->placeholder('Ex: Outfit'),
                        Textarea::make('settings.website.scripts.head')
                            ->label('Scripts no <head>')
                            ->helperText('Ex: Google Analytics, Tag Manager, Facebook Pixel.')
                            ->rows(4),
                        Textarea::make('settings.website.scripts.footer')
                            ->label('Scripts no <footer>')
                            ->helperText('Scripts que devem ser carregados ao final da página.')
                            ->rows(4),
                    ]),
            ])
            ->statePath('data');
    }

    public function getSubheading(): ?string
    {
        return 'Configure as informações globais do site, SEO, scripts e menus de navegação.';
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('save')
                ->label('Salvar Alterações')
                ->action('save')
                ->color('primary'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $setting = Setting::firstOrCreate(['id' => 1]);
        
        $currentSettings = $setting->settings ?? [];
        $data['settings'] = array_replace_recursive($currentSettings, $data['settings'] ?? []);

        $setting->update([
            'settings' => $data['settings'],
        ]);

        Notification::make()
            ->success()
            ->title('Configurações salvas')
            ->send();
    }
}
