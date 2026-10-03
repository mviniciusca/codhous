<?php

namespace App\Livewire;

use App\Models\OperationArea;
use App\Services\PostcodeFinderService;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class SectionCoverage extends Component
{
    public $cep = '';
    public $error = '';
    public $success = false;
    public $addressText = '';

    public $title = null;
    public $subtitle = null;
    public $description = null;
    public $cities = null;
    public $sidebar = null;
    public $backgroundMedia = null;
    public $bgColor = null;
    public $textColor = 'light';
    public $header = [];

    public function mount($title = null, $subtitle = null, $description = null, $cities = null, $sidebar = null, $backgroundMedia = null, $header = [])
    {
        $this->title = $title;
        $this->subtitle = $subtitle;
        $this->description = $description;
        $this->cities = $cities;
        $this->sidebar = $sidebar;
        $this->backgroundMedia = $backgroundMedia;
        $this->header = $header;
    }

    public function updatedCep($value)
    {
        $digits = preg_replace('/[^0-9]/', '', $value);
        $digits = substr($digits, 0, 8);
        if ($digits === '') {
            $this->cep = '';
            return;
        }
        $this->cep = strlen($digits) <= 5
            ? $digits
            : substr($digits, 0, 5) . '-' . substr($digits, 5, 3);
    }

    public function lookupCep()
    {
        $this->error = '';
        $this->success = false;
        $this->addressText = '';

        $cleanCep = preg_replace('/[^0-9]/', '', $this->cep);
        if (strlen($cleanCep) !== 8) {
            $this->error = 'Por favor, digite um CEP válido com 8 dígitos.';
            return;
        }

        if (! OperationArea::isCepInOperationArea($cleanCep)) {
            $this->error = 'Este CEP está fora da nossa área de atendimento.';
            return;
        }

        $addressData = [];

        try {
            $service = new PostcodeFinderService($cleanCep, function ($key, $value) use (&$addressData) {
                $k = str_replace('content.', '', $key);
                $addressData[$k] = $value;
            });
            $service->find();

            if (! empty($addressData['street'])) {
                $this->success = true;
                $this->addressText = "{$addressData['street']} - {$addressData['neighborhood']}, {$addressData['city']} - {$addressData['state']}";
            } else {
                $this->error = 'Atendimento não encontrado para este CEP.';
            }
        } catch (ValidationException $e) {
            $this->error = 'Atendimento não encontrado para este CEP.';
        } catch (\Exception $e) {
            $this->error = 'Erro ao buscar o CEP.';
        }
    }

    public function resetCepForm()
    {
        $this->cep = '';
        $this->error = '';
        $this->success = false;
        $this->addressText = '';
    }

    public function render()
    {
        return view('livewire.section-coverage');
    }
}
