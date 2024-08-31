<?php

namespace App\Filament\Resources\SettingResource\Pages;

use App\Filament\Resources\SettingResource;
use Filament\Actions;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditLayout extends EditRecord
{
    protected static string $resource = SettingResource::class;
    protected static ?string $navigationIcon = 'heroicon-o-paint-brush';
    public static function getNavigationLabel(): string
    {
        return __('Design & Appearance');
    }
    public function getTitle(): string|Htmlable
    {
        return __('Design & Appearance Settings');
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
                                    ->helperText(__('Set the background position. Default value is "top"'))
                                    ->required()
                                    ->options([
                                        'bg-top' => __('Top'),
                                        'bg-center' => __('Center'),
                                        'bg-left' => __('Left'),
                                        'bg-right' => __('Right'),
                                    ])
                                    ->default('bg-top'),
                                Select::make('content.bg_repeat')
                                    ->label(__('Background Repeat'))
                                    ->helperText(__('Repeat the background image. Default value is "no repeat"'))
                                    ->required()
                                    ->options([
                                        'bg-no-repeat' => __('No Repeat'),
                                        'bg-repeat' => __('Repeat'),
                                        'bg-repeat-x' => __('Repeat X'),
                                        'bg-repeat-y' => __('Repeat Y'),
                                    ])
                                    ->default('bg-no-repeat'),
                                Select::make('content.bg_attachment')
                                    ->required()
                                    ->label(__('Background Attachment'))
                                    ->helperText(__('Set the background attachment. Default value is "scroll"'))
                                    ->options([
                                        'bg-scroll' => __('Scroll'),
                                        'bg-fixed' => __('Fixed'),
                                    ])
                                    ->default('bg-scroll'),
                                Select::make('content.bg_size')
                                    ->label(__('Background Size'))
                                    ->helperText(__('Set the background size. Default value is "cover"'))
                                    ->required()
                                    ->options([
                                        'bg-cover' => __('Cover'),
                                        'bg-auto' => __('Auto'),
                                        'bg-contain' => __('Contain'),
                                    ])
                                    ->default('bg-cover'),
                                TextInput::make('content.bg_height')
                                    ->label(__('Background Hight'))
                                    ->helperText(__('Height in pixels (px). Default value is 680'))
                                    ->numeric()
                                    ->default(680)
                                    ->required()
                                    ->maxLength(10)
                                    ->suffix('px')
                            ]),
                        FileUpload::make('background_image')
                            ->label(__('Background Image'))
                            ->image()
                            ->imageEditor()
                            ->directory('background')
                            ->columnSpan(3)
                            ->required()
                            ->helperText(__('Upload the application background image')),
                    ]),
            ]);
    }
}
