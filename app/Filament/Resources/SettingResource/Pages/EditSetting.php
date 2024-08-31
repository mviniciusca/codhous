<?php

namespace App\Filament\Resources\SettingResource\Pages;

use Filament\Actions;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\SettingResource;
use Filament\Forms\Components\TextInput;
use Illuminate\Contracts\Support\Htmlable;

class EditSetting extends EditRecord
{
    protected static string $resource = SettingResource::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    public static function getNavigationLabel(): string
    {
        return __('App Settings');
    }
    public function getTitle(): string|Htmlable
    {
        return __('App Settings');
    }


    protected function getHeaderActions(): array
    {
        return [
            //Actions\DeleteAction::make(),
        ];
    }

}
