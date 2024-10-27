<?php

namespace App\Services;

use Exception;
use Filament\Forms\Set;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Filament\Forms\Components\Livewire;
use Filament\Notifications\Notification;
use Illuminate\Validation\ValidationException;

class PostcodeFinder
{
    /**
     * Create a new class instance.
     */
    private ?string $postcode = null;
    private string $apiFormatReturn;
    private string $apiEndpoint;
    private array|Response $response;

    private string $state;

    public function __construct(string $state, private Set $set)
    {
        $this->state = $state;
        $this->set = $set;
        $this->postcode = $this->postcodePattern();
        $this->apiEndpoint = env('VIACEP_API_ENDPOINT');
        $this->apiFormatReturn = env('VIACEP_API_FORMAT');
    }

    private function reachEndpoint(): array|Response
    {
        $this->response = Http::get($this->endpoint())
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
            $this->clearState();
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

    private function clearState()
    {
        $this->set('content.neighborhood', null);
        $this->set('content.street', null);
        $this->set('content.number', null);
        $this->set('content.city', null);
        $this->set('content.state', null);

        return $this;
    }

    private function endpoint(): string
    {
        return "{$this->apiEndpoint}/{$this->postcode}/{$this->apiFormatReturn}/";
    }

    private function postcodePattern(): array|string|null
    {
        return preg_replace('/[^0-9]/', '', $this->state);
    }
}
