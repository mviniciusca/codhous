<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\PostcodeFinderService;
use Illuminate\Validation\ValidationException;

class SectionHeroCep extends Component
{
    public $cep = '';
    public $error = '';
    public $success = false;
    public $addressText = '';

    public function updatedCep($value)
    {
        // Simple numeric cleanup, mask applied on frontend or let Livewire handle it
        $this->cep = preg_replace('/(\d{5})(\d{3})/', '$1-$2', preg_replace('/[^0-9]/', '', $value));
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

        $addressData = [];

        try {
            $service = new PostcodeFinderService($cleanCep, function ($key, $value) use (&$addressData) {
                // Remove 'content.' prefix for keys set by the service
                $k = str_replace('content.', '', $key);
                $addressData[$k] = $value;
            });
            $service->find();

            if (!empty($addressData['street'])) {
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
        $website = \App\Models\Setting::get('website', []);
        $homepage = data_get($website, 'homepage', []);
        $slideshow = data_get($homepage, 'slideshow', []);

        $mainSlide = count($slideshow) > 0 ? $slideshow[0] : [
            'title' => 'Concreto usinado com agilidade e precisao no traco',
            'subtitle' => 'Entrega rapida, rastreamento em tempo real e suporte tecnico especializado para garantir o sucesso da sua obra do inicio ao fim.',
        ];

        return view('livewire.section-hero-cep', [
            'mainSlide' => $mainSlide,
        ]);
    }
}
