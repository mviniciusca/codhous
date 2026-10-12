<?php

namespace App\Filament\Resources;

use App\Enums\AccessArea;
use App\Filament\Concerns\BelongsToArea;
use App\Filament\Resources\SettingResource\Pages;
use App\Models\Setting;
use Filament\Forms\Form;
use Filament\Resources\Resource;

class SettingResource extends Resource
{
    use BelongsToArea;

    public static function getAccessArea(): AccessArea
    {
        return AccessArea::Configuracoes;
    }

    protected static ?string $model = Setting::class;

    protected static ?string $modelLabel = 'configuração';

    protected static ?string $pluralModelLabel = 'configurações';

    public static function getNavigationGroup(): ?string
    {
        return 'Configurações';
    }

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Configurações';

    public static function getNavigationUrl(): string
    {
        return Pages\EditCompany::getUrl(['record' => Setting::first()?->id ?? 1]);
    }

    public static function form(Form $form): Form
    {
        // O formulário é definido na página de edição
        return $form;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSettings::route('/'),
            'company' => Pages\EditCompany::route('/{record}/company'),
        ];
    }
}
