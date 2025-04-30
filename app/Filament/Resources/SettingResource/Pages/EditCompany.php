<?php

namespace App\Filament\Resources\SettingResource\Pages;

use App\Filament\Resources\SettingResource;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;

class EditCompany extends EditRecord
{
    protected static string $resource = SettingResource::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    public static function getNavigationLabel(): string
    {
        return __('Company Information');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('Company Information'))
                    ->icon('heroicon-o-building-office')
                    ->description(__('Manager your Company Information. This information it will be used in Budget or Invoice Documents.'))
                    ->relationship('companySetting')
                    ->columns(2)
                    ->schema([
                        TextInput::make('trade_name')
                            ->required()
                            ->maxLength(255)
                            ->helperText(__('Company Name. Visible in Budget / Invoice Document'))
                            ->label(__('Company Name')),
                        TextInput::make('legal_name')
                            ->required()
                            ->maxLength(255)
                            ->helperText(__('Legal Name. Visible in Budget / Invoice Document'))
                            ->label(__('Company Legal Name / Trade Name')),
                        TextInput::make('phone')
                            ->tel()
                            ->required()
                            ->maxLength(255)
                            ->prefix('+'.env('COUNTRY_CODE'))
                            ->mask('(99) 9999-9999')
                            ->helperText(__('Visible in Budget / Invoice Document'))
                            ->label(__('Phone Number')),
                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->helperText(__('Visible in Budget / Invoice Document'))
                            ->label(__('Email Address')),
                        TextInput::make('cnpj')
                            ->required()
                            ->maxLength(255)
                            ->mask('99.999.999/9999-99')
                            ->helperText(__('Visible in Budget / Invoice Document'))
                            ->label(__('CNPJ'))
                            ->columnSpanFull(),
                    ]),
                Section::make(__('Company Address'))
                    ->icon('heroicon-o-map')
                    ->description(__('Manager your Company Information. This information it will be used in Budget or Invoice Documents.'))
                    ->relationship('companySetting')
                    ->columns(2)
                    ->schema([
                        TextInput::make('address.postcode')
                            ->required()
                            ->maxLength(255)
                            ->label(__('Postcode'))
                            ->helperText(__('Company Postcode'))
                            ->mask('99999-999')
                            ->prefixIcon('heroicon-o-map-pin')
                            ->placeholder('----- ---')
                            ->live(true)
                            ->suffixAction(
                                fn ($state, Set $set) => Action::make('search-cep')
                                    ->icon('heroicon-o-magnifying-glass')
                                    ->tooltip(__('Search address by postcode'))
                                    ->action(function () use ($state, $set) {
                                        try {
                                            if (empty($state)) {
                                                throw new \Exception('CEP inválido');
                                            }

                                            // Limpar máscara e obter apenas números
                                            $cep = preg_replace('/[^0-9]/', '', $state);

                                            // Verificar se o CEP tem 8 dígitos
                                            if (strlen($cep) !== 8) {
                                                throw new \Exception('CEP deve ter 8 dígitos');
                                            }

                                            // Fazer a requisição à API do ViaCEP
                                            $response = \Illuminate\Support\Facades\Http::get("https://viacep.com.br/ws/{$cep}/json/");

                                            // Verificar se a requisição foi bem-sucedida
                                            if (! $response->successful()) {
                                                throw new \Exception('Erro ao buscar CEP: '.$response->status());
                                            }

                                            // Obter os dados da resposta
                                            $data = $response->json();

                                            // Verificar se o CEP foi encontrado
                                            if (isset($data['erro']) && $data['erro'] === true) {
                                                throw new \Exception('CEP não encontrado');
                                            }

                                            // Preencher os campos com os dados retornados
                                            $set('address.street', $data['logradouro'] ?? '');
                                            $set('address.neighborhood', $data['bairro'] ?? '');
                                            $set('address.city', $data['localidade'] ?? '');
                                            $set('address.state', $data['uf'] ?? '');

                                            // Notificação de sucesso
                                            \Filament\Notifications\Notification::make()
                                                ->title('CEP encontrado!')
                                                ->success()
                                                ->send();
                                        } catch (\Exception $e) {
                                            // Em caso de erro, exibir notificação
                                            \Filament\Notifications\Notification::make()
                                                ->title('Erro')
                                                ->body($e->getMessage())
                                                ->danger()
                                                ->send();
                                        }
                                    })
                            ),
                        TextInput::make('address.number')
                            ->maxLength(255)
                            ->helperText(__('Company Street Number (optional)'))
                            ->label(__('Number (optional)')),
                        TextInput::make('address.neighborhood')
                            ->required()
                            ->maxLength(255)
                            ->helperText(__('Company Neighborhood'))
                            ->label(__('Neighborhood')),
                        TextInput::make('address.city')
                            ->required()
                            ->maxLength(255)
                            ->helperText(__('Company City'))
                            ->label(__('City')),
                        TextInput::make('address.state')
                            ->live()
                            ->debounce(500)
                            ->required()
                            ->maxLength(2)
                            ->afterStateUpdated(function (Set $set, ?string $state) {
                                return $set('address.state', Str::upper($state));
                            })
                            ->helperText(__('Company Federal State'))
                            ->label(__('UF')),
                    ]),
                Section::make(__('Budget / Invoice Document Settings'))
                    ->icon('heroicon-o-document')
                    ->description(__('Define information that will be displayed at the end of each Budget or Invoice Document.'))
                    ->relationship('companySetting')
                    ->columns(2)
                    ->schema([
                        Textarea::make('budget_information')
                            ->helperText(__('Enter your information here (optional)'))
                            ->label(__('Information (optional)'))
                            ->rows(5)
                            ->maxLength(300)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
