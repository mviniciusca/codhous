<?php

namespace App\Filament\Resources\SettingResource\Pages;

use App\Filament\Resources\SettingResource;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;

class EditWebsite extends EditRecord
{
    protected static string $resource = SettingResource::class;

    protected static ?string $navigationLabel = 'Website e SEO';

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';

    public function getTitle(): string
    {
        return 'Configurações do Website';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
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
                        Repeater::make('settings.website.navigation')
                            ->label('Links do Menu')
                            ->helperText('Adicione ou remova itens do menu superior.')
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
                                    ->inline(false),
                                Toggle::make('settings.website.features.budget_tool')
                                    ->label('Ferramenta de Orçamento')
                                    ->helperText('Permite que os clientes solicitem orçamentos online.')
                                    ->inline(false),
                            ]),
                    ]),

                Section::make('Atendimento via WhatsApp')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->description('Configure o widget flutuante de atendimento direto.')
                    ->schema([
                        Toggle::make('settings.website.features.whatsapp_widget.enabled')
                            ->label('Habilitar Widget')
                            ->helperText('Ativa o botão flutuante do WhatsApp no canto da tela.')
                            ->reactive(),
                        Grid::make(2)
                            ->visible(fn($get) => $get('settings.website.features.whatsapp_widget.enabled'))
                            ->schema([
                                TextInput::make('settings.website.features.whatsapp_widget.number')
                                    ->label('Número do WhatsApp')
                                    ->helperText('Formato: 55 + DDD + Número (apenas números).')
                                    ->placeholder('5511999999999')
                                    ->tel(),
                                TextInput::make('settings.website.features.whatsapp_widget.message')
                                    ->label('Mensagem Inicial')
                                    ->helperText('Texto que será pré-preenchido para o cliente.')
                                    ->placeholder('Olá, gostaria de saber mais...'),
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
                        TextInput::make('settings.website.scripts.google_fonts')
                            ->label('URL Google Fonts')
                            ->helperText('Link da família de fontes selecionada.')
                            ->placeholder('https://fonts.googleapis.com/...'),
                        Textarea::make('settings.website.scripts.head')
                            ->label('Scripts no <head>')
                            ->helperText('Ex: Google Analytics, Tag Manager, Facebook Pixel.')
                            ->rows(4),
                        Textarea::make('settings.website.scripts.footer')
                            ->label('Scripts no <footer>')
                            ->helperText('Scripts que devem ser carregados ao final da página.')
                            ->rows(4),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getRedirectUrl(): string
    {
        return static::getUrl(['record' => $this->getRecord()]);
    }
}
