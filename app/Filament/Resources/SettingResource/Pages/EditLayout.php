<?php

namespace App\Filament\Resources\SettingResource\Pages;

use App\Filament\Resources\SettingResource;
use Filament\Actions;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;

class EditLayout extends EditRecord
{
    protected static string $resource = SettingResource::class;

    protected static ?string $navigationIcon = 'heroicon-o-paint-brush';

    public static function getNavigationLabel(): string
    {
        return __('Design & Appearance');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('Logo & Favicons'))
                    ->description(__('Define the Appearance and Layout of the Application'))
                    ->icon('heroicon-o-paint-brush')
                    ->relationship('layout')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        FileUpload::make('logo')
                            ->label(__('App Logo'))
                            ->image()
                            ->imageEditor()
                            ->directory('layout')
                            ->helperText(__('Upload the application logo')),
                        FileUpload::make('favicon')
                            ->label(__('App Favicon'))
                            ->image()
                            ->imageEditor()
                            ->directory('layout')
                            ->helperText(__('Upload the application favicon')),
                    ]),
                Section::make(__('Background Image'))
                    ->description(__('Configure the background image of the application'))
                    ->icon('heroicon-o-photo')
                    ->relationship('layout')
                    ->collapsible()
                    ->schema([
                        Fieldset::make(__('Module Settings'))
                            ->schema([
                                Toggle::make('content.status')
                                    ->default(true)
                                    ->label(__('Display Background Image')),
                            ]),
                        Fieldset::make(__('Background Settings'))
                            ->columns(3)
                            ->schema([
                                Select::make('content.bg_position')
                                    ->label(__('Background Position'))
                                    ->helperText(__('Set the background position. Default value is "center"'))
                                    ->options([
                                        'center' => __('Center'),
                                        'left' => __('Left'),
                                        'right' => __('Right'),
                                    ])
                                    ->default('center'),
                                Select::make('content.bg_repeat')
                                    ->label(__('Background Repeat'))
                                    ->helperText(__('Repeat the background image. Default value is "no repeat"'))
                                    ->options([
                                        'no-repeat' => __('No Repeat'),
                                        'repeat' => __('Repeat'),
                                        'repeat-x' => __('Repeat X'),
                                        'repeat-y' => __('Repeat Y'),
                                    ])
                                    ->default('no-repeat'),
                                Select::make('content.bg_attachment')
                                    ->label(__('Background Attachment'))
                                    ->helperText(__('Set the background attachment. Default value is "scroll"'))
                                    ->options([
                                        'scroll' => __('Scroll'),
                                        'fixed' => __('Fixed'),
                                    ])
                                    ->default('scroll'),
                            ]),
                        FileUpload::make('background_image')
                            ->label(__('Background Image'))
                            ->image()
                            ->imageEditor()
                            ->directory('background')
                            ->columnSpan(3)
                            ->helperText(__('Upload the application background image')),
                    ]),
            ]);
    }
}
