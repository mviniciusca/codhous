<?php

namespace App\Filament\Resources\VolumeFormulaResource\Pages;

use App\Filament\Resources\VolumeFormulaResource;
use App\Models\VolumeFormula;
use App\Services\VolumeCalculator\VolumeFormulaPresets;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

/**
 * @property VolumeFormula $record
 */
class EditVolumeFormula extends EditRecord
{
    protected static string $resource = VolumeFormulaResource::class;

    /** Versão criada no último salvamento (null = a regra não mudou) */
    protected ?string $savedVersionLabel = null;

    public function getSubheading(): ?string
    {
        $version = $this->record->currentVersion;

        return $version
            ? 'Versão atual: '.$version->label().', de '.$version->created_at?->format('d/m/Y H:i').($version->author ? ' por '.$version->author->name : '').'.'
            : null;
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $definition = $this->record->definition()?->toArray() ?? [];

        if ($definition !== []) {
            $definition['margin_percent'] = $definition['margin_percent'] + 0;
            $definition['rounding_step'] = $definition['rounding_step'] ?: null;
        }

        return array_merge($data, $definition, ['version_notes' => null]);
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        /** @var VolumeFormula $record */
        $version = DB::transaction(function () use ($record, $data) {
            $record->update(Arr::only($data, ['name', 'description', 'shape', 'is_active']));

            return $record->saveVersion(
                VolumeFormulaResource::definitionFromState(Arr::only($data, VolumeFormulaResource::DEFINITION_FIELDS)),
                auth()->user(),
                $data['version_notes'] ?? null,
            );
        });

        $this->data['version_notes'] = null;
        $this->savedVersionLabel = $version?->label();

        return $record;
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title($this->savedVersionLabel ? 'Fórmula salva como '.$this->savedVersionLabel : 'Fórmula salva')
            ->body($this->savedVersionLabel ? 'Os cálculos já feitos continuam com a versão anterior.' : 'A regra de cálculo não mudou, então nenhuma versão nova foi criada.');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('restoreDefault')
                ->label('Restaurar padrão')
                ->icon('heroicon-o-arrow-uturn-left')
                ->color('gray')
                ->visible(fn () => $this->record->is_system && VolumeFormulaPresets::definitionFor($this->record->slug))
                ->requiresConfirmation()
                ->modalDescription('Cria uma nova versão com a regra original do sistema. As versões anteriores continuam no histórico.')
                ->action(function () {
                    $version = $this->record->saveVersion(VolumeFormulaPresets::definitionFor($this->record->slug), auth()->user(), 'Restaurada a regra padrão do sistema');

                    Notification::make()
                        ->title($version ? 'Regra padrão restaurada como '.$version->label() : 'A fórmula já usa a regra padrão')
                        ->success()
                        ->send();

                    $this->redirect(static::getUrl(['record' => $this->record]));
                }),
            DeleteAction::make()
                ->visible(fn () => $this->record->canBeDeleted()),
        ];
    }
}
