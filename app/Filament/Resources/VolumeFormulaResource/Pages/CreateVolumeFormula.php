<?php

namespace App\Filament\Resources\VolumeFormulaResource\Pages;

use App\Filament\Resources\VolumeFormulaResource;
use App\Models\VolumeFormula;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class CreateVolumeFormula extends CreateRecord
{
    protected static string $resource = VolumeFormulaResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            $formula = VolumeFormula::create(Arr::only($data, ['name', 'description', 'shape', 'is_active']) + [
                'sort' => (int) VolumeFormula::max('sort') + 1,
            ]);

            $formula->saveVersion(
                VolumeFormulaResource::definitionFromState(Arr::only($data, VolumeFormulaResource::DEFINITION_FIELDS)),
                auth()->user(),
                'Primeira versão',
            );

            return $formula;
        });
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->getRecord()]);
    }
}
