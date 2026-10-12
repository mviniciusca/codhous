<?php

namespace App\Filament\Resources\VolumeFormulaResource\Pages;

use App\Filament\Resources\VolumeFormulaResource;
use App\Services\VolumeCalculator\TruckMinimum;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListVolumeFormulas extends ListRecords
{
    protected static string $resource = VolumeFormulaResource::class;

    public function getSubheading(): ?string
    {
        return 'Regras de cálculo usadas pela calculadora de volume. Cada alteração vira uma nova versão, e os cálculos salvos guardam a versão usada.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('truckMinimum')
                ->label('Pedido mínimo do caminhão')
                ->icon('heroicon-o-truck')
                ->color('gray')
                ->modalHeading('Pedido mínimo do caminhão betoneira')
                ->modalDescription('Quando o volume calculado fica abaixo deste mínimo, as calculadoras do painel e do site avisam e sugerem alternativas, como virar o concreto na obra.')
                ->modalWidth('md')
                ->modalSubmitActionLabel('Salvar')
                ->fillForm(fn () => TruckMinimum::settings())
                ->form([
                    Toggle::make('enabled')
                        ->label('Avisar volumes abaixo do mínimo')
                        ->live(),
                    TextInput::make('min_volume')
                        ->label('Volume mínimo por caminhão')
                        ->helperText('Pedido mínimo praticado pela empresa. O usual é 3 m³.')
                        ->numeric()
                        ->minValue(0.5)
                        ->maxValue(20)
                        ->step(0.5)
                        ->suffix('m³')
                        ->required()
                        ->visible(fn (Get $get) => (bool) $get('enabled')),
                ])
                ->action(function (array $data) {
                    TruckMinimum::save((bool) $data['enabled'], (float) ($data['min_volume'] ?? TruckMinimum::settings()['min_volume']));

                    Notification::make()->title('Pedido mínimo atualizado')->success()->send();
                }),
            CreateAction::make()->label('Nova fórmula')->icon('heroicon-o-plus'),
        ];
    }
}
