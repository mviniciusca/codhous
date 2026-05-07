<?php

namespace App\Filament\Resources\SettingResource\Pages;

use App\Filament\Resources\SettingResource;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;

class EditSecurity extends EditRecord
{
    protected static string $resource = SettingResource::class;

    protected static ?string $navigationLabel = 'Segurança e Acesso';

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    public function getTitle(): string
    {
        return 'Segurança e Acesso';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Controle de Acesso e Manutenção')
                    ->icon('heroicon-o-lock-closed')
                    ->description('Gerencie a visibilidade do site e restrições de IP.')
                    ->schema([
                        Toggle::make('settings.security.maintenance_mode')
                            ->label('Modo Manutenção')
                            ->helperText('Quando ativado, os visitantes verão apenas a página de manutenção.')
                            ->reactive()
                            ->inline(false),
                        Textarea::make('settings.security.maintenance_message')
                            ->label('Mensagem de Manutenção')
                            ->helperText('Texto exibido para os visitantes enquanto o site estiver em manutenção.')
                            ->visible(fn($get) => $get('settings.security.maintenance_mode'))
                            ->rows(3),
                    ]),

                Section::make('Cloudflare Turnstile (Anti-Spam)')
                    ->icon('heroicon-o-shield-check')
                    ->description('Proteja seus formulários contra bots de forma invisível.')
                    ->schema([
                        Toggle::make('settings.security.turnstile.enabled')
                            ->label('Habilitar Turnstile')
                            ->helperText('Ativa a proteção anti-spam nos formulários de contato e orçamento.')
                            ->reactive()
                            ->inline(false),
                        \Filament\Forms\Components\Grid::make(2)
                            ->visible(fn($get) => $get('settings.security.turnstile.enabled'))
                            ->schema([
                                \Filament\Forms\Components\TextInput::make('settings.security.turnstile.site_key')
                                    ->label('Site Key')
                                    ->placeholder('1x00000000000000000000AA')
                                    ->required(fn($get) => $get('settings.security.turnstile.enabled')),
                                \Filament\Forms\Components\TextInput::make('settings.security.turnstile.secret_key')
                                    ->label('Secret Key')
                                    ->placeholder('1x0000000000000000000000000000000AA')
                                    ->required(fn($get) => $get('settings.security.turnstile.enabled'))
                                    ->password()
                                    ->revealable(),
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
        return 'Gerencie as configurações de segurança, modo manutenção e proteções anti-spam.';
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
