<?php

namespace App\Services;

use Exception;
use Filament\Forms\Set;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Filament\Forms\Components\Livewire;
use Illuminate\Validation\ValidationException;

class PostcodeFinder
{
    /**
     * Create a new class instance.
     */
    private ?string $postcode = null;
    private string $apiFormatReturn = 'json';
    private string $apiEndpoint = 'https://viacep.com.br/ws';
    private array|Response $response;

    public function __construct(string $state, private Set $set, private $livewire)
    {
        $this->set = $set;
        $this->livewire = $livewire;
        $this->postcode = preg_replace('/[^0-9]/', '', $state);
    }

    private function reachEndpoint(): array|Response
    {
        $this->response = Http::get("{$this->apiEndpoint}/{$this->postcode}/{$this->apiFormatReturn}/")
            ->throw()
            ->json();

        $this->validateResponse();

        return $this->response;

    }

    private function setData(): static
    {

        $this->set('content.neighborhood', $this->response['bairro'] ?? null);
        $this->set('content.street', $this->response['logradouro'] ?? null);
        $this->set('content.city', $this->response['localidade'] ?? null);
        $this->set('content.state', $this->response['uf'] ?? null);

        return $this;
    }

    public function find()
    {

        try {
            $this->emptyState();
            $this->livewire->validateOnly('data.content.postcode');
            $this->reachEndpoint();
            return $this->setData();
        } catch (Exception $e) {
            $this->validateResponse();
        }
    }

    private function validateResponse()
    {
        if (isset($this->response['erro'])) {
            throw ValidationException::withMessages([
                'data.content.postcode' => __('CEP not Found'),
            ]);
        }
    }

    private function set($key, $value)
    {
        return ($this->set)($key, $value);
    }

    private function emptyState()
    {
        $this->set('content.neighborhood', null);
        $this->set('content.street', null);
        $this->set('content.number', null);
        $this->set('content.city', null);
        $this->set('content.state', null);

        return $this;
    }
}
