<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SettingResource\Pages;
use App\Models\Setting;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\Page;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static ?string $navigationGroup = 'Administração';

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Configurações';

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function getNavigationUrl(): string
    {
        return Pages\EditWebsite::getUrl(['record' => 1]);
    }

    public static function form(Form $form): Form
    {
        // O formulário agora é definido individualmente em cada página de edição
        return $form;
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\EditWebsite::class,
            Pages\EditCompany::class,
            Pages\EditSecurity::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSettings::route('/'),
            'website' => Pages\EditWebsite::route('/{record}/website'),
            'company' => Pages\EditCompany::route('/{record}/company'),
            'security' => Pages\EditSecurity::route('/{record}/security'),
        ];
    }
}
