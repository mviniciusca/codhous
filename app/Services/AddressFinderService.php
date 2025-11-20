<?php

namespace App\Services;

use Exception;
use Filament\Forms\Set;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AddressFinderService
{
    protected string $postcode;

    protected $set;

    protected array $response = [];

    protected array $fieldMap;

    protected string $validationField;

    /**
     * Create a new instance of the address finder
     *
     * @param string $postcode O CEP a ser buscado
     * @param Set $set Função do Filament para definir valores de campo
     * @param array $fieldMap Mapeamento de campos da API para campos do formulário
     * @param string $validationField Campo a ser usado na mensagem de validação
     */
    public function __construct(string $postcode, $set, array $fieldMap, string $validationField = 'postcode')
    {
        $this->postcode = $this->formatPostcode($postcode);
        $this->set = $set;
        $this->fieldMap = $fieldMap;
        $this->validationField = $validationField;
    }

    /**
     * Busca o CEP na API ViaCEP e preenche os campos do formulário
     */
    public function find(): bool
    {
        try {
            Log::debug("Buscando CEP: {$this->postcode}");

            // Limpar os campos antes de preencher
            $this->clearFields();

            // Buscar o CEP na API
            $url = "https://viacep.com.br/ws/{$this->postcode}/json/";
            $response = Http::get($url);

            if (! $response->successful()) {
                throw new Exception('Erro ao acessar serviço de CEP: '.$response->status());
            }

            $this->response = $response->json();
            Log::debug('Resposta da API:', $this->response);

            // Verificar se o CEP foi encontrado
            if (isset($this->response['erro']) && $this->response['erro'] === true) {
                throw ValidationException::withMessages([
                    $this->validationField => __('CEP não encontrado'),
                ]);
            }

            // Preencher os campos do formulário
            $this->fillFields();

            return true;
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            Log::error('Erro ao buscar CEP: '.$e->getMessage());
            throw ValidationException::withMessages([
                $this->validationField => __('Erro ao buscar CEP: '.$e->getMessage()),
            ]);
        }
    }

    /**
     * Preenche os campos do formulário com os dados do endereço
     */
    protected function fillFields(): void
    {
        Log::debug('Preenchendo campos usando mapeamento:', $this->fieldMap);

        foreach ($this->fieldMap as $apiField => $formField) {
            $value = $this->response[$apiField] ?? '';
            Log::debug("Definindo campo {$formField} com valor de {$apiField}: {$value}");
            ($this->set)($formField, $value);
        }
    }

    /**
     * Limpa os campos do formulário antes de preenchê-los
     */
    protected function clearFields(): void
    {
        foreach ($this->fieldMap as $apiField => $formField) {
            ($this->set)($formField, null);
        }
    }

    /**
     * Formata o CEP removendo caracteres não numéricos
     */
    protected function formatPostcode(string $postcode): string
    {
        return preg_replace('/[^0-9]/', '', $postcode);
    }
}
