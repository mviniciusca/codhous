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
                            ->helperText('Quando ativado, apenas usuários com IPs permitidos verão o site.')
                            ->reactive()
                            ->inline(false),
                        Textarea::make('settings.security.maintenance_message')
                            ->label('Mensagem de Manutenção')
                            ->helperText('Texto exibido para os visitantes enquanto o site estiver em manutenção.')
                            ->visible(fn($get) => $get('settings.security.maintenance_mode'))
                            ->rows(3),
                        TagsInput::make('settings.security.allowed_ips')
                            ->label('Lista de IPs Permitidos')
                            ->helperText('Adicione os endereços IP que devem ter acesso total, ignorando o modo manutenção.')
                            ->placeholder('Ex: 192.168.1.1')
                            ->suggestions([
                                request()->ip(),
                            ]),
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
