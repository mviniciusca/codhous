<?php

namespace App\Filament\Resources\SettingResource\Pages;

use App\Filament\Resources\SettingResource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;

class EditCompany extends EditRecord
{
    protected static string $resource = SettingResource::class;

    protected static ?string $navigationLabel = 'Dados da Empresa';

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    public function getTitle(): string
    {
        return 'Dados da Empresa';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                \Filament\Forms\Components\Tabs::make('Configurações')
                    ->columnSpanFull()
                    ->persistTabInQueryString()
                    ->tabs([
                        \Filament\Forms\Components\Tabs\Tab::make('Dados Institucionais')
                            ->icon('heroicon-o-building-office')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('settings.company.trade_name')
                                            ->label('Nome Fantasia')
                                            ->prefixIcon('heroicon-o-building-storefront')
                                            ->helperText('O nome pelo qual sua empresa é conhecida publicamente.')
                                            ->required(),
                                        TextInput::make('settings.company.legal_name')
                                            ->label('Razão Social')
                                            ->prefixIcon('heroicon-o-scale')
                                            ->helperText('Nome oficial registrado em cartório.'),
                                        TextInput::make('settings.company.document')
                                            ->label('CNPJ')
                                            ->prefixIcon('heroicon-o-identification')
                                            ->helperText('Cadastro Nacional da Pessoa Jurídica.')
                                            ->mask('99.999.999/9999-99')
                                            ->placeholder('00.000.000/0000-00'),
                                        TextInput::make('settings.company.ie')
                                            ->label('Inscrição Estadual')
                                            ->prefixIcon('heroicon-o-document-text')
                                            ->helperText('Registro de contribuinte do ICMS.'),
                                        TextInput::make('settings.company.im')
                                            ->label('Inscrição Municipal')
                                            ->prefixIcon('heroicon-o-document-text')
                                            ->helperText('Registro de contribuinte municipal (ISS).'),
                                        TextInput::make('settings.company.email')
                                            ->label('E-mail Comercial')
                                            ->prefixIcon('heroicon-o-envelope')
                                            ->helperText('Endereço de e-mail para contatos oficiais.')
                                            ->email(),
                                        TextInput::make('settings.company.phone')
                                            ->label('Telefone Principal')
                                            ->prefixIcon('heroicon-o-phone')
                                            ->helperText('Telefone fixo ou celular com DDD.')
                                            ->tel(),
                                    ]),
                            ]),

                        \Filament\Forms\Components\Tabs\Tab::make('Localização')
                            ->icon('heroicon-o-map-pin')
                            ->schema([
                                TextInput::make('settings.company.address.postcode')
                                    ->label('CEP')
                                    ->prefixIcon('heroicon-o-map')
                                    ->helperText('Código de Endereçamento Postal.')
                                    ->mask('99999-999')
                                    ->placeholder('00000-000')
                                    ->live()
                                    ->afterStateUpdated(function ($state, \Filament\Forms\Set $set) {
                                        if (strlen($state ?? '') === 9) {
                                            (new \App\Services\AddressFinderService(
                                                $state,
                                                $set,
                                                [
                                                    'logradouro' => 'settings.company.address.street',
                                                    'bairro' => 'settings.company.address.neighborhood',
                                                    'localidade' => 'settings.company.address.city',
                                                    'uf' => 'settings.company.address.state',
                                                ],
                                                'settings.company.address.postcode'
                                            ))->find();
                                        }
                                    }),
                                Grid::make(3)
                                    ->schema([
                                        TextInput::make('settings.company.address.street')
                                            ->label('Logradouro')
                                            ->prefixIcon('heroicon-o-map-pin')
                                            ->helperText('Rua, Avenida, etc.')
                                            ->columnSpan(2),
                                        TextInput::make('settings.company.address.number')
                                            ->label('Número')
                                            ->prefixIcon('heroicon-o-hashtag')
                                            ->helperText('Número do imóvel ou S/N.')
                                            ->columnSpan(1),
                                        TextInput::make('settings.company.address.neighborhood')
                                            ->label('Bairro')
                                            ->prefixIcon('heroicon-o-home')
                                            ->helperText('Nome do bairro.'),
                                        TextInput::make('settings.company.address.city')
                                            ->label('Cidade')
                                            ->prefixIcon('heroicon-o-building-office')
                                            ->helperText('Cidade da seu sede.'),
                                        TextInput::make('settings.company.address.state')
                                            ->label('Estado/UF')
                                            ->prefixIcon('heroicon-o-globe-americas')
                                            ->helperText('Sigla do estado (Ex: SP).'),
                                    ]),
                                \Filament\Forms\Components\Textarea::make('settings.company.maps_code')
                                    ->label('Código de Incorporação (Google Maps)')
                                    ->helperText('Cole aqui o <iframe> gerado pelo Google Maps no menu "Compartilhar > Incorporar um mapa".')
                                    ->rows(3)
                                    ->placeholder('<iframe src="..."></iframe>'),
                            ]),

                        \Filament\Forms\Components\Tabs\Tab::make('Horário de Atendimento')
                            ->icon('heroicon-o-clock')
                            ->schema([
                                TextInput::make('settings.company.opening_hours')
                                    ->label('Horário de Funcionamento')
                                    ->prefixIcon('heroicon-o-clock')
                                    ->helperText('Descreva o horário de atendimento (Ex: Segunda a Sexta, 08:00 às 18:00).')
                                    ->placeholder('Segunda a Sexta, 08:00 às 18:00')
                                    ->required(),
                            ]),

                        \Filament\Forms\Components\Tabs\Tab::make('Informações de Orçamento')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                \Filament\Forms\Components\FileUpload::make('settings.company.invoice_logo')
                                    ->label('Logo para Documentos (PDF)')
                                    ->helperText('Logo que aparecerá no topo dos orçamentos e notas. Use formatos transparentes (PNG/SVG) para melhor resultado.')
                                    ->image()
                                    ->directory('logos')
                                    ->visibility('public')
                                    ->columnSpanFull(),
                                \Filament\Forms\Components\Textarea::make('settings.company.budget_information')
                                    ->label('Textos Padrões e Termos')
                                    ->helperText('Condições de pagamento, validade, prazos e observações legais que aparecerão no rodapé do PDF.')
                                    ->rows(6)
                                    ->columnSpanFull(),
                            ]),

                        \Filament\Forms\Components\Tabs\Tab::make('Locação de Equipamentos')
                            ->icon('heroicon-o-truck')
                            ->schema([
                                \Filament\Forms\Components\Toggle::make('settings.company.equipment_rental.is_active')
                                    ->label('Ativar Módulo de Locação')
                                    ->helperText('Suspender ou ativar o módulo de locação globalmente no site inteiro.')
                                    ->onIcon('heroicon-o-check')
                                    ->default(true),
                                TextInput::make('settings.company.equipment_rental.whatsapp_number')
                                    ->label('WhatsApp Direto para Locação')
                                    ->prefixIcon('heroicon-o-chat-bubble-left-right')
                                    ->helperText('Se em branco, usará o número padrão do site para enviar a mensagem de locação.')
                                    ->tel(),
                                \Filament\Forms\Components\Toggle::make('settings.company.equipment_rental.redirect_to_contact')
                                    ->label('Redirecionar para página personalizada')
                                    ->helperText('Se ativado, ao invés do WhatsApp, o botão redirecionará o usuário para o endereço informado abaixo.')
                                    ->onIcon('heroicon-o-check')
                                    ->live(),
                                TextInput::make('settings.company.equipment_rental.contact_page_url')
                                    ->label('Endereço da Página de Atendimento')
                                    ->prefixIcon('heroicon-o-link')
                                    ->helperText('Digite a URL para onde o botão deve redirecionar (ex: /contato ou https://site.com/form).')
                                    ->visible(fn (\Filament\Forms\Get $get) => $get('settings.company.equipment_rental.redirect_to_contact')),
                            ]),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getSubheading(): ?string
    {
        return 'Gerencie as informações institucionais, contatos e endereço da sua empresa.';
    }

    protected function getRedirectUrl(): string
    {
        return static::getUrl(['record' => $this->getRecord()]);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $currentSettings = $this->getRecord()->settings ?? [];
        $data['settings'] = array_replace_recursive($currentSettings, $data['settings']);
        
        return $data;
    }
}
