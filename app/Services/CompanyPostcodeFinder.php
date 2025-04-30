<?php

namespace App\Services;

use Exception;
use Filament\Forms\Set;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class CompanyPostcodeFinder
{
    private string $state;

    private string $postcode;

    private array $response = [];

    private $set;

    public function __construct(string $state, $set)
    {
        $this->set = $set;
        $this->state = $state;
        $this->postcode = $this->formatPostcode($state);
    }

    public function find()
    {
        try {
            // Montar URL da API
            $url = "https://viacep.com.br/ws/{$this->postcode}/json/";

            // Fazer a requisição HTTP
            $response = Http::get($url);

            // Log da resposta para debug
            Log::debug("Buscando CEP: {$this->postcode}");
            Log::debug('Resposta da API:', $response->json());

            // Verificar se a requisição foi bem-sucedida
            if ($response->successful()) {
                $data = $response->json();

                // Verificar se o CEP foi encontrado
                if (isset($data['erro']) && $data['erro'] === true) {
                    throw ValidationException::withMessages([
                        'data.companySetting.address.postcode' => __('CEP não encontrado'),
                    ]);
                }

                // Preencher os campos com a resposta
                $this->setData($data);

                return true;
            } else {
                throw new Exception('Erro ao acessar o serviço de CEP. Status: '.$response->status());
            }
        } catch (ValidationException $e) {
            // Re-lançar exceções de validação
            throw $e;
        } catch (Exception $e) {
            // Log do erro
            Log::error('Erro ao buscar CEP: '.$e->getMessage());
            throw $e;
        }
    }

    private function setData(array $data)
    {
        Log::debug('Preenchendo campos com dados:', $data);

        // Usando a função set para definir os valores nos campos do formulário
        ($this->set)('companySetting.address.street', $data['logradouro'] ?? '');
        ($this->set)('companySetting.address.neighborhood', $data['bairro'] ?? '');
        ($this->set)('companySetting.address.city', $data['localidade'] ?? '');
        ($this->set)('companySetting.address.state', $data['uf'] ?? '');

        Log::debug('Campos definidos com sucesso');
    }

    private function formatPostcode(string $cep): string
    {
        // Remover caracteres não numéricos
        return preg_replace('/[^0-9]/', '', $cep);
    }
}
