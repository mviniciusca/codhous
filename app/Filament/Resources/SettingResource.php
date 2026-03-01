<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SettingResource\Pages;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Configurações';

    public static function getNavigationUrl(): string
    {
        return static::getUrl('edit', ['record' => 1]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Configurações')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Website')
                            ->icon('heroicon-o-globe-alt')
                            ->schema([
                                Forms\Components\Tabs::make('Website Sections')
                                    ->tabs([
                                        Forms\Components\Tabs\Tab::make('Geral & SEO')
                                            ->icon('heroicon-o-information-circle')
                                            ->schema([
                                                Forms\Components\Section::make('Informações Gerais')
                                                    ->schema([
                                                        Forms\Components\TextInput::make('settings.website.name')
                                                            ->label('Nome do Website')
                                                            ->required(),
                                                        Forms\Components\TextInput::make('settings.website.title')
                                                            ->label('Título (SEO)')
                                                            ->placeholder('Ex: Nome da Empresa - Slogan'),
                                                        Forms\Components\Textarea::make('settings.website.description')
                                                            ->label('Descrição (SEO)')
                                                            ->rows(3),
                                                    ])->columns(2),

                                                Forms\Components\Section::make('Navegação Principal')
                                                    ->schema([
                                                        Forms\Components\Repeater::make('settings.website.navigation')
                                                            ->label('Links do Menu')
                                                            ->schema([
                                                                Forms\Components\TextInput::make('label')
                                                                    ->label('Rótulo')
                                                                    ->required(),
                                                                Forms\Components\TextInput::make('url')
                                                                    ->label('URL/Rota')
                                                                    ->required(),
                                                            ])
                                                            ->columns(2)
                                                            ->grid(2)
                                                            ->defaultItems(0)
                                                            ->itemLabel(fn(array $state): ?string => $state['label'] ?? null),
                                                    ]),
                                            ]),

                                        Forms\Components\Tabs\Tab::make('Funcionalidades')
                                            ->icon('heroicon-o-cpu-chip')
                                            ->schema([
                                                Forms\Components\Grid::make(2)
                                                    ->schema([
                                                        Forms\Components\Section::make('Ferramentas')
                                                            ->columnSpan(1)
                                                            ->schema([
                                                                Forms\Components\Toggle::make('settings.website.features.concrete_calculator')
                                                                    ->label('Habilitar Calculadora de Concreto')
                                                                    ->helperText('Exibir a ferramenta de cálculo de volume na frente do site.'),
                                                                Forms\Components\Toggle::make('settings.website.features.budget_tool')
                                                                    ->label('Habilitar Ferramenta de Orçamento')
                                                                    ->helperText('Permitir que clientes solicitem orçamentos online.'),
                                                            ]),

                                                        Forms\Components\Section::make('Widget WhatsApp')
                                                            ->columnSpan(1)
                                                            ->schema([
                                                                Forms\Components\Toggle::make('settings.website.features.whatsapp_widget.enabled')
                                                                    ->label('Habilitar Widget de Chat')
                                                                    ->reactive(),
                                                                Forms\Components\TextInput::make('settings.website.features.whatsapp_widget.number')
                                                                    ->label('Número do WhatsApp')
                                                                    ->placeholder('5511999999999')
                                                                    ->tel()
                                                                    ->visible(fn($get) => $get('settings.website.features.whatsapp_widget.enabled')),
                                                                Forms\Components\Textarea::make('settings.website.features.whatsapp_widget.message')
                                                                    ->label('Mensagem Inicial')
                                                                    ->placeholder('Olá, gostaria de saber mais...')
                                                                    ->visible(fn($get) => $get('settings.website.features.whatsapp_widget.enabled')),
                                                            ]),
                                                    ]),
                                            ]),

                                        Forms\Components\Tabs\Tab::make('Homepage')
                                            ->icon('heroicon-o-home')
                                            ->schema([
                                                Forms\Components\Section::make('Slideshow Principal')
                                                    ->description('Gerencie os banners que aparecem na página inicial.')
                                                    ->schema([
                                                        Forms\Components\Repeater::make('settings.website.homepage.slideshow')
                                                            ->label('Slides')
                                                            ->schema([
                                                                Forms\Components\FileUpload::make('image')
                                                                    ->label('Imagem do Slide')
                                                                    ->image()
                                                                    ->directory('slideshow')
                                                                    ->required(),
                                                                Forms\Components\TextInput::make('title')
                                                                    ->label('Título do Slide'),
                                                                Forms\Components\TextInput::make('subtitle')
                                                                    ->label('Subtítulo/Descrição'),
                                                                Forms\Components\TextInput::make('link')
                                                                    ->label('Link do Botão (URL)'),
                                                            ])
                                                            ->columns(2)
                                                            ->grid(2)
                                                            ->itemLabel(fn(array $state): ?string => $state['title'] ?? null),
                                                    ]),
                                            ]),

                                        Forms\Components\Tabs\Tab::make('Redes & Scripts')
                                            ->icon('heroicon-o-code-bracket')
                                            ->schema([
                                                Forms\Components\Grid::make(2)
                                                    ->schema([
                                                        Forms\Components\Section::make('Redes Sociais')
                                                            ->columnSpan(1)
                                                            ->schema([
                                                                Forms\Components\TextInput::make('settings.website.social_networks.instagram')
                                                                    ->label('Instagram URL')
                                                                    ->prefix('instagr.am/'),
                                                                Forms\Components\TextInput::make('settings.website.social_networks.facebook')
                                                                    ->label('Facebook URL')
                                                                    ->prefix('fb.com/'),
                                                                Forms\Components\TextInput::make('settings.website.social_networks.linkedin')
                                                                    ->label('LinkedIn URL')
                                                                    ->prefix('linkedin.com/in/'),
                                                                Forms\Components\TextInput::make('settings.website.social_networks.whatsapp')
                                                                    ->label('WhatsApp Link')
                                                                    ->placeholder('https://wa.me/55...'),
                                                            ]),

                                                        Forms\Components\Section::make('Scripts Adicionais')
                                                            ->columnSpan(1)
                                                            ->schema([
                                                                Forms\Components\TextInput::make('settings.website.scripts.google_fonts')
                                                                    ->label('Google Fonts URL')
                                                                    ->placeholder('https://fonts.googleapis.com/...'),
                                                                Forms\Components\Textarea::make('settings.website.scripts.head')
                                                                    ->label('Scripts Head')
                                                                    ->placeholder('Ex: Google Analytics, Pixel Facebook')
                                                                    ->rows(4),
                                                                Forms\Components\Textarea::make('settings.website.scripts.footer')
                                                                    ->label('Scripts Footer')
                                                                    ->rows(4),
                                                            ]),
                                                    ]),
                                            ]),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('Empresa')
                            ->icon('heroicon-o-building-office-2')
                            ->schema([
                                Forms\Components\Section::make('Dados Institucionais')
                                    ->schema([
                                        Forms\Components\TextInput::make('settings.company.trade_name')
                                            ->label('Nome Fantasia'),
                                        Forms\Components\TextInput::make('settings.company.legal_name')
                                            ->label('Razão Social'),
                                        Forms\Components\TextInput::make('settings.company.cnpj')
                                            ->label('CNPJ')
                                            ->mask('99.999.999/9999-99'),
                                        Forms\Components\TextInput::make('settings.company.email')
                                            ->label('E-mail de Contato')
                                            ->email(),
                                        Forms\Components\TextInput::make('settings.company.phone')
                                            ->label('Telefone/WhatsApp')
                                            ->tel(),
                                    ])->columns(2),

                                Forms\Components\Section::make('Localização e Mapas')
                                    ->schema([
                                        Forms\Components\Grid::make(3)
                                            ->schema([
                                                Forms\Components\TextInput::make('settings.company.address.street')
                                                    ->label('Rua')
                                                    ->columnSpan(2),
                                                Forms\Components\TextInput::make('settings.company.address.number')
                                                    ->label('Número')
                                                    ->columnSpan(1),
                                                Forms\Components\TextInput::make('settings.company.address.neighborhood')
                                                    ->label('Bairro'),
                                                Forms\Components\TextInput::make('settings.company.address.city')
                                                    ->label('Cidade'),
                                                Forms\Components\TextInput::make('settings.company.address.state')
                                                    ->label('Estado'),
                                                Forms\Components\TextInput::make('settings.company.address.zip_code')
                                                    ->label('CEP')
                                                    ->mask('99999-999'),
                                            ]),
                                        Forms\Components\TextInput::make('settings.company.maps_link')
                                            ->label('Link Google Maps')
                                            ->hint('Link para o botão "Como Chegar"'),
                                    ]),

                                Forms\Components\Section::make('Horário de Funcionamento')
                                    ->schema([
                                        Forms\Components\Repeater::make('settings.company.opening_hours')
                                            ->label('Dias e Horários')
                                            ->schema([
                                                Forms\Components\TextInput::make('day')->label('Dia'),
                                                Forms\Components\TextInput::make('hours')->label('Horário'),
                                            ])
                                            ->columns(2)
                                            ->grid(2),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('Segurança')
                            ->icon('heroicon-o-shield-check')
                            ->schema([
                                Forms\Components\Section::make('Manutenção e Acesso')
                                    ->schema([
                                        Forms\Components\Toggle::make('settings.security.maintenance_mode')
                                            ->label('Ativar Modo Manutenção')
                                            ->helperText('Quando ativado, o site exibirá apenas a mensagem de manutenção para os visitantes.')
                                            ->reactive(),
                                        Forms\Components\Textarea::make('settings.security.maintenance_message')
                                            ->label('Mensagem de Manutenção')
                                            ->visible(fn($get) => $get('settings.security.maintenance_mode'))
                                            ->columnSpanFull(),
                                        Forms\Components\TagsInput::make('settings.security.allowed_ips')
                                            ->label('IPs Permitidos')
                                            ->placeholder('Adicionar IP...')
                                            ->helperText('IPs que podem acessar o site mesmo em modo manutenção.'),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSettings::route('/'),
            'create' => Pages\CreateSetting::route('/create'),
            'edit' => Pages\EditSetting::route('/{record}/edit'),
        ];
    }
}
