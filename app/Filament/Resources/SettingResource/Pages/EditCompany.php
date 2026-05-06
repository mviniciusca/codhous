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
                Section::make('Dados Institucionais')
                    ->icon('heroicon-o-building-office')
                    ->description('Informações legais e comerciais da sua empresa.')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('settings.company.trade_name')
                                    ->label('Nome Fantasia')
                                    ->helperText('O nome pelo qual sua empresa é conhecida publicamente.')
                                    ->required(),
                                TextInput::make('settings.company.legal_name')
                                    ->label('Razão Social')
                                    ->helperText('Nome oficial registrado em cartório.'),
                                TextInput::make('settings.company.document')
                                    ->label('CNPJ')
                                    ->helperText('Cadastro Nacional da Pessoa Jurídica.')
                                    ->mask('99.999.999/9999-99')
                                    ->placeholder('00.000.000/0000-00'),
                                TextInput::make('settings.company.ie')
                                    ->label('Inscrição Estadual')
                                    ->helperText('Registro de contribuinte do ICMS.'),
                                TextInput::make('settings.company.im')
                                    ->label('Inscrição Municipal')
                                    ->helperText('Registro de contribuinte municipal (ISS).'),
                                TextInput::make('settings.company.email')
                                    ->label('E-mail Comercial')
                                    ->helperText('Endereço de e-mail para contatos oficiais.')
                                    ->email(),
                                TextInput::make('settings.company.phone')
                                    ->label('Telefone Principal')
                                    ->helperText('Telefone fixo ou celular com DDD.')
                                    ->tel(),
                            ]),
                    ]),

                Section::make('Localização')
                    ->icon('heroicon-o-map-pin')
                    ->description('Endereço físico e links para mapas.')
                    ->schema([
                        TextInput::make('settings.company.address.postcode')
                            ->label('CEP')
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
                                    ->helperText('Rua, Avenida, etc.')
                                    ->columnSpan(2),
                                TextInput::make('settings.company.address.number')
                                    ->label('Número')
                                    ->helperText('Número do imóvel ou S/N.')
                                    ->columnSpan(1),
                                TextInput::make('settings.company.address.neighborhood')
                                    ->label('Bairro')
                                    ->helperText('Nome do bairro.'),
                                TextInput::make('settings.company.address.city')
                                    ->label('Cidade')
                                    ->helperText('Cidade da seu sede.'),
                                TextInput::make('settings.company.address.state')
                                    ->label('Estado/UF')
                                    ->helperText('Sigla do estado (Ex: SP).'),
                            ]),
                        TextInput::make('settings.company.maps_link')
                            ->label('Link do Google Maps')
                            ->helperText('URL para o botão "Como Chegar" (Ex: Google My Business).')
                            ->placeholder('https://goo.gl/maps/...'),
                    ]),

                Section::make('Horário de Atendimento')
                    ->icon('heroicon-o-clock')
                    ->description('Defina os períodos em que sua empresa está aberta ao público.')
                    ->schema([
                        TextInput::make('settings.company.opening_hours')
                            ->label('Horário de Funcionamento')
                            ->helperText('Descreva o horário de atendimento (Ex: Segunda a Sexta, 08:00 às 18:00).')
                            ->placeholder('Segunda a Sexta, 08:00 às 18:00')
                            ->required(),
                    ]),

                Section::make('Informações de Orçamento')
                    ->icon('heroicon-o-document-text')
                    ->description('Textos padrões que aparecem nos orçamentos gerados.')
                    ->schema([
                        \Filament\Forms\Components\Textarea::make('settings.company.budget_information')
                            ->label('Informações Adicionais')
                            ->helperText('Ex: Condições de pagamento, prazos de entrega, etc.')
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

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $currentSettings = $this->getRecord()->settings ?? [];
        $data['settings'] = array_replace_recursive($currentSettings, $data['settings']);
        
        return $data;
    }
}
