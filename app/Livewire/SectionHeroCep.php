<?php

namespace App\Livewire;

use App\Models\OperationArea;
use App\Services\PostcodeFinderService;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

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

    /** Tema: default, corporate, creative */
    public string $theme = 'default';
    public ?string $bgColor = null;
    public string $textColor = 'light';

    /** Estatísticas exibidas na hero (value + label) */
    public array $stats = [];

    /** Botões de ação adicionais */
    public array $buttons = [];

    /** Controles de visibilidade */
    public bool $showActionButtons = true;
    public bool $showStats = true;
    public bool $showSlideshow = false;

    /** Slideshow items */
    public array $slideshow = [];

    public function updatedCep($value)
    {
        $digits = preg_replace('/[^0-9]/', '', $value);
        $digits = substr($digits, 0, 8); // CEP tem apenas 8 dígitos — corta se digitar rápido a mais
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

        // Verifica se o CEP está na área de operação (restrição por Operation Area)
        if (! OperationArea::isCepInOperationArea($cleanCep)) {
            $this->error = 'Este CEP está fora da nossa área de atendimento. No momento atendemos apenas a região do Rio de Janeiro e Grande Rio.';
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
        $mainSlide = $this->mainSlide ?: [
            'title' => 'Concreto usinado com agilidade e precisão no traço',
            'subtitle' => 'Entrega rápida, rastreamento em tempo real e suporte técnico especializado.',
        ];
        $stats = $this->stats ?: [
            ['value' => '2.500+', 'label' => 'Obras Entregues'],
            ['value' => '150k+', 'label' => 'm³ de Concreto'],
            ['value' => '15+', 'label' => 'Anos de Mercado'],
        ];

        return view('livewire.section-hero-cep', [
            'mainSlide' => $mainSlide,
            'badge' => $this->badge ?: 'Qualidade Certificada',
            'layout' => $this->layout ?: 'default',
            'theme' => $this->theme ?: 'default',
            'stats' => $stats,
            'buttons' => $this->buttons,
            'showActionButtons' => $this->showActionButtons,
            'showStats' => $this->showStats,
        ]);
    }
}
