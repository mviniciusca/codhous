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

    /** Dados do slide (título/subtítulo) vindos da hero ativa ou do section-home */
    public array $mainSlide = [];

    /** Texto do badge (ex: Qualidade Certificada) */
    public string $badge = 'Qualidade Certificada';

    /** Layout: default (texto + CEP lado a lado) | whatsapp (destaque central + CEP abaixo) */
    public string $layout = 'default';

    /** Estatísticas exibidas na hero (value + label) */
    public array $stats = [];

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
        $mainSlide = $this->mainSlide ?: [
            'title' => 'Concreto usinado com agilidade e precisão no traço',
            'subtitle' => 'Entrega rápida, rastreamento em tempo real e suporte técnico especializado.',
        ];
        $stats = $this->stats ?: [
            ['value' => '500+', 'label' => 'Obras atendidas'],
            ['value' => '98%', 'label' => 'Pontualidade'],
            ['value' => '15+', 'label' => 'Anos de experiência'],
        ];

        return view('livewire.section-hero-cep', [
            'mainSlide' => $mainSlide,
            'badge' => $this->badge ?: 'Qualidade Certificada',
            'layout' => $this->layout ?: 'default',
            'stats' => $stats,
        ]);
    }
}
