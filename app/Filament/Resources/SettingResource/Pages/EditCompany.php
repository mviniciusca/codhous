<?php

namespace App\Filament\Resources\SettingResource\Pages;

use App\Filament\Resources\AlertResource;
use App\Filament\Resources\SettingResource;
use App\Models\Alert;
use App\Models\Setting;
use App\Services\AddressFinderService;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\HtmlString;

/**
 * Configurações da empresa e do acesso ao site numa página só, centralizada:
 * seções com título à esquerda e campos à direita, salvar no cabeçalho.
 */
class EditCompany extends EditRecord
{
    protected static string $resource = SettingResource::class;

    protected static string $view = 'filament.settings.page';

    public function getTitle(): string
    {
        return 'Configurações';
    }

    public function getSubheading(): ?string
    {
        return 'Dados da empresa, endereço, orçamentos, locação e segurança do site.';
    }

    public function getMaxContentWidth(): MaxWidth
    {
        return MaxWidth::FiveExtraLarge;
    }

    public function getBreadcrumbs(): array
    {
        return [];
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
                ->action(function (): void {
                    $this->fillForm();
                    $this->dispatch('settings-saved');

                    Notification::make()->title('Alterações descartadas')->send();
                })
                // Só aparece com alterações pendentes (a view marca <html class="st-dirty">)
                ->extraAttributes(['data-st-discard' => true]),
            Action::make('save')
                ->label('Salvar alterações')
                ->icon('heroicon-o-check')
                ->keyBindings(['mod+s'])
                ->action('save'),
        ];
    }

    protected function getFormActions(): array
    {
        return [];
    }

    public function form(Form $form): Form
    {
        return $form
            ->columns(1)
            ->schema([
                ...$this->companySections(),
                ...$this->addressSections(),
                ...$this->budgetSections(),
                ...$this->rentalSections(),
                ...$this->securitySections(),
            ]);
    }

    protected function companySections(): array
    {
        return [
            Section::make('Identificação')
                ->description('Aparece no rodapé do site e no cabeçalho dos orçamentos.')
                ->aside()
                ->schema([
                    Grid::make(['default' => 1, 'sm' => 2])->schema([
                        TextInput::make('settings.company.trade_name')
                            ->label('Nome fantasia')
                            ->prefixIcon('heroicon-o-building-storefront')
                            ->helperText('Nome pelo qual a empresa é conhecida.')
                            ->required(),
                        TextInput::make('settings.company.legal_name')
                            ->label('Razão social')
                            ->prefixIcon('heroicon-o-scale')
                            ->helperText('Nome oficial registrado.'),
                        TextInput::make('settings.company.document')
                            ->label('CNPJ')
                            ->prefixIcon('heroicon-o-identification')
                            ->helperText('Sai no cabeçalho do PDF.')
                            ->mask('99.999.999/9999-99')
                            ->placeholder('00.000.000/0000-00'),
                        TextInput::make('settings.company.ie')
                            ->label('Inscrição estadual')
                            ->prefixIcon('heroicon-o-document-text')
                            ->helperText('Contribuinte do ICMS. Opcional.'),
                        TextInput::make('settings.company.im')
                            ->label('Inscrição municipal')
                            ->prefixIcon('heroicon-o-document-text')
                            ->helperText('Contribuinte do ISS. Opcional.'),
                    ]),
                ]),

            Section::make('Contato')
                ->description('Canais oficiais mostrados aos clientes.')
                ->aside()
                ->schema([
                    Grid::make(['default' => 1, 'sm' => 2])->schema([
                        TextInput::make('settings.company.email')
                            ->label('E-mail comercial')
                            ->prefixIcon('heroicon-o-envelope')
                            ->helperText('Para contatos oficiais.')
                            ->email(),
                        TextInput::make('settings.company.phone')
                            ->label('Telefone')
                            ->prefixIcon('heroicon-o-phone')
                            ->helperText('Fixo ou celular, com DDD.')
                            ->placeholder('(21) 90000-0000')
                            ->tel(),
                    ]),
                    TextInput::make('settings.company.opening_hours')
                        ->label('Horário de atendimento')
                        ->prefixIcon('heroicon-o-clock')
                        ->placeholder('Segunda a Sexta, 08:00 às 18:00')
                        ->helperText('Exibido no topo do site e na seção de mapa.')
                        ->required(),
                ]),
        ];
    }

    protected function addressSections(): array
    {
        return [
            Section::make('Sede')
                ->description('Digite o CEP para preencher o restante automaticamente.')
                ->aside()
                ->schema([
                    Grid::make(['default' => 1, 'sm' => 6])->schema([
                        TextInput::make('settings.company.address.postcode')
                            ->label('CEP')
                            ->prefixIcon('heroicon-o-map')
                            ->helperText('Preenche o endereço.')
                            ->mask('99999-999')
                            ->placeholder('00000-000')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (?string $state, Set $set): void {
                                if (strlen($state ?? '') !== 9) {
                                    return;
                                }

                                (new AddressFinderService(
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
                            })
                            ->columnSpan(['sm' => 2]),
                        TextInput::make('settings.company.address.street')
                            ->label('Logradouro')
                            ->prefixIcon('heroicon-o-map-pin')
                            ->helperText('Rua, avenida, estrada.')
                            ->columnStart(['sm' => 1])
                            ->columnSpan(['sm' => 4]),
                        TextInput::make('settings.company.address.number')
                            ->label('Número')
                            ->prefixIcon('heroicon-o-hashtag')
                            ->helperText('Ou S/N.')
                            ->placeholder('S/N')
                            ->columnSpan(['sm' => 2]),
                        TextInput::make('settings.company.address.neighborhood')
                            ->label('Bairro')
                            ->prefixIcon('heroicon-o-home')
                            ->helperText('Bairro da sede.')
                            ->columnSpan(['sm' => 2]),
                        TextInput::make('settings.company.address.city')
                            ->label('Cidade')
                            ->prefixIcon('heroicon-o-building-office')
                            ->helperText('Cidade da sede.')
                            ->columnSpan(['sm' => 2]),
                        TextInput::make('settings.company.address.state')
                            ->label('UF')
                            ->prefixIcon('heroicon-o-globe-americas')
                            ->helperText('Sigla do estado.')
                            ->placeholder('RJ')
                            ->maxLength(2)
                            ->columnSpan(['sm' => 2]),
                    ]),
                ]),

            Section::make('Mapa')
                ->description('Mostrado na seção de mapa do site.')
                ->aside()
                ->schema([
                    Textarea::make('settings.company.maps_code')
                        ->label('Código do Google Maps')
                        ->helperText('No Google Maps: Compartilhar › Incorporar um mapa › Copiar HTML.')
                        ->placeholder('<iframe src="https://www.google.com/maps/embed?..."></iframe>')
                        ->rows(4)
                        ->extraInputAttributes(['class' => 'font-mono text-xs', 'spellcheck' => 'false']),
                ]),
        ];
    }

    protected function budgetSections(): array
    {
        return [
            Section::make('Documento PDF')
                ->description('Logo e textos do orçamento enviado ao cliente.')
                ->aside()
                ->schema([
                    FileUpload::make('settings.company.invoice_logo')
                        ->label('Logo do documento')
                        ->helperText('PNG ou SVG com fundo transparente.')
                        ->image()
                        ->imagePreviewHeight('96')
                        ->directory('logos')
                        ->visibility('public'),
                    Textarea::make('settings.company.budget_information')
                        ->label('Condições e observações')
                        ->helperText('Pagamento, validade e prazos. Vai no rodapé do PDF.')
                        ->rows(6),
                ]),
        ];
    }

    protected function rentalSections(): array
    {
        return [
            Section::make('Locação de equipamentos')
                ->description('Como o visitante pede um equipamento pelo site.')
                ->aside()
                ->schema([
                    Toggle::make('settings.company.equipment_rental.is_active')
                        ->label('Locação disponível no site')
                        ->onIcon('heroicon-m-check')
                        ->helperText('Desligada, a vitrine de equipamentos mostra o serviço como suspenso.')
                        // O site trata a chave ausente como ativa
                        ->afterStateHydrated(fn (Toggle $component, $state) => $component->state($state ?? true))
                        ->live(),
                    TextInput::make('settings.company.equipment_rental.whatsapp_number')
                        ->label('WhatsApp da locação')
                        ->prefixIcon('heroicon-o-chat-bubble-left-right')
                        ->placeholder('(21) 90000-0000')
                        ->helperText('Em branco, usa o WhatsApp principal do site.')
                        ->tel()
                        ->visible(fn (Get $get): bool => (bool) $get('settings.company.equipment_rental.is_active')),
                    Toggle::make('settings.company.equipment_rental.redirect_to_contact')
                        ->label('Enviar para uma página em vez do WhatsApp')
                        ->onIcon('heroicon-m-check')
                        ->helperText('O botão de locação abre a página informada abaixo.')
                        ->live()
                        ->visible(fn (Get $get): bool => (bool) $get('settings.company.equipment_rental.is_active')),
                    TextInput::make('settings.company.equipment_rental.contact_page_url')
                        ->label('Endereço da página')
                        ->prefixIcon('heroicon-o-link')
                        ->placeholder('/atendimento')
                        ->helperText('Caminho do site (/contato) ou link completo.')
                        ->visible(fn (Get $get): bool => $get('settings.company.equipment_rental.is_active')
                            && $get('settings.company.equipment_rental.redirect_to_contact')),
                ]),
        ];
    }

    protected function securitySections(): array
    {
        return [
            Section::make('Modo manutenção')
                ->description('Tira o site do ar para os visitantes. O painel continua acessível.')
                ->aside()
                ->schema([
                    Toggle::make('settings.security.maintenance_mode')
                        ->label('Site em manutenção')
                        ->onIcon('heroicon-m-check')
                        ->helperText('Visitantes veem só a página de manutenção.')
                        ->live(),
                    Textarea::make('settings.security.maintenance_message')
                        ->label('Mensagem para os visitantes')
                        ->helperText('Exibida na página de manutenção.')
                        ->rows(3)
                        ->visible(fn (Get $get): bool => (bool) $get('settings.security.maintenance_mode')),
                ]),

            Section::make('Anti-spam')
                ->description('Cloudflare Turnstile nos formulários de contato e orçamento.')
                ->aside()
                ->schema([
                    Toggle::make('settings.security.turnstile.enabled')
                        ->label('Proteção ativa')
                        ->onIcon('heroicon-m-check')
                        ->helperText('Bloqueia robôs sem pedir nada ao visitante.')
                        ->live(),
                    Grid::make(['default' => 1, 'sm' => 2])
                        ->visible(fn (Get $get): bool => (bool) $get('settings.security.turnstile.enabled'))
                        ->schema([
                            TextInput::make('settings.security.turnstile.site_key')
                                ->label('Site key')
                                ->prefixIcon('heroicon-o-key')
                                ->helperText('Chave pública do painel da Cloudflare.')
                                ->placeholder('1x00000000000000000000AA')
                                ->required(fn (Get $get): bool => (bool) $get('settings.security.turnstile.enabled')),
                            TextInput::make('settings.security.turnstile.secret_key')
                                ->label('Secret key')
                                ->prefixIcon('heroicon-o-lock-closed')
                                ->helperText('Chave secreta. Não compartilhe.')
                                ->placeholder('1x0000000000000000000000000000000AA')
                                ->required(fn (Get $get): bool => (bool) $get('settings.security.turnstile.enabled'))
                                ->password()
                                ->revealable(),
                        ]),
                ]),

            Section::make('Privacidade (LGPD)')
                ->description('O aviso de cookies é um alerta do site.')
                ->aside()
                ->schema([
                    Placeholder::make('cookie_consent_alert')
                        ->hiddenLabel()
                        ->content(function (): HtmlString {
                            $alert = Alert::where('style', Alert::STYLE_CONSENT)->orderBy('id')->first();
                            $url = $alert
                                ? AlertResource::getUrl('edit', ['record' => $alert])
                                : AlertResource::getUrl('create');
                            $status = match (true) {
                                ! $alert => ['Ainda não criado', 'gray'],
                                $alert->status() === Alert::STATUS_ACTIVE => ['Ativo no site', 'success'],
                                default => ['Fora do ar', 'warning'],
                            };

                            return new HtmlString(
                                '<span class="st-status cx-tone-'.$status[1].'"><span class="cx-dot"></span>'.e($status[0]).'</span>'
                                .'<a href="'.e($url).'" class="st-link">'.($alert ? 'Editar aviso de cookies' : 'Criar aviso de cookies').' →</a>'
                            );
                        }),
                ]),
        ];
    }

    protected function getRedirectUrl(): ?string
    {
        // Fica na mesma tela depois de salvar
        return null;
    }

    protected function afterSave(): void
    {
        $this->dispatch('settings-saved');
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['settings'] = Setting::merge($this->getRecord()->settings ?? [], $data['settings'] ?? []);

        return $data;
    }
}
