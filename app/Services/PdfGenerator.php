<?php

namespace App\Services;

use App\Models\Budget;
use App\Models\Product;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;
use Spatie\LaravelPdf\Facades\Pdf;

class PdfGenerator
{
    /**
     * Pdf Generator
     */
    public string $format;

    public string $downloadPath;

    public ?string $filePath = null;

    public function __construct(private ?array $state)
    {
        $this->state = $state;
        $this->downloadPath = rtrim(env('PDF_DOWNLOAD_PATH', 'app/public/'), '/').'/';
        $this->format = 'a4';
    }

    /**
     * Summary of generateFilename
     * @return string
     */
    public function generateFilename(): string
    {
        return Str::slug($this->state['code']
            .$this->state['id'])
            .now()->format('YmdHis')
            .(env('PDF_TERMINATION', '.pdf'));
    }

    /**
     * Summary of generate
     * @return mixed|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function generate()
    {
        $this->filePath = $this->savePath();

        // Get product name, but ensure it's properly formatted
        $productName = $this->getProductName();

        try {
            // Verificar a estrutura do state e ajustar se necessário
            $state = $this->state;

            // Se content não estiver no formato de array e for apenas um objeto/array associativo
            if (isset($state['content']) && ! isset($state['content'][0]) && is_array($state['content'])) {
                // Colocar content em um array para compatibilidade com o template
                $state['content'] = [$state['content']];
            }

            // Log para depuração
            \Log::info('PDF Generation - State Structure:', [
                'state'            => json_encode($state),
                'content_exists'   => isset($state['content']),
                'content_is_array' => isset($state['content']) ? is_array($state['content']) : false,
                'content_has_0'    => isset($state['content'][0]),
            ]);

            Pdf::view('pdf.invoice', [
                'state'        => $state,
                'product_name' => $productName,
            ])
                ->format($this->format)
                ->save($this->filePath);

            $persist = Budget::query()
                ->where('id', '=', $this->state['id'])
                ->update([
                    'pdf_document' => $this->generateFilename(),
                ]);

            if (! $persist) {
                return Notification::make()
                    ->title(__('Error on save the document on database.'))
                    ->warning()
                    ->send();
            }

            Notification::make()
                ->title(__('Document generated with success!'))
                ->success()
                ->send();

            return $this->downloadPdf();
        } catch (\Exception $e) {
            \Log::error('PDF Generation Error: '.$e->getMessage());
            \Log::error('PDF Generation Error Stack: '.$e->getTraceAsString());

            Notification::make()
                ->title(__('Error generating PDF: ').$e->getMessage())
                ->danger()
                ->send();

            return null;
        }
    }

    /**
     * Prepara um array completo com todos os detalhes do pedido
     * @return array
     */
    protected function prepareOrderDetails()
    {
        // Buscar nome do produto
        $productName = $this->getProductName();

        // Inicializar o array com informações básicas
        $details = [
            'state'        => $this->state,
            'product_name' => $productName,
            'order'        => [
                'id'         => $this->state['id'] ?? null,
                'code'       => $this->state['code'] ?? null,
                'status'     => $this->state['status'] ?? null,
                'created_at' => $this->state['created_at'] ?? null,
                'updated_at' => $this->state['updated_at'] ?? null,
            ],
            'customer' => [
                'name'  => $this->state['content'][0]['customer_name'] ?? null,
                'email' => $this->state['content'][0]['customer_email'] ?? null,
                'phone' => $this->state['content'][0]['customer_phone'] ?? null,
            ],
            'address' => [
                'street'       => $this->state['content'][0]['street'] ?? null,
                'number'       => $this->state['content'][0]['number'] ?? null,
                'neighborhood' => $this->state['content'][0]['neighborhood'] ?? null,
                'city'         => $this->state['content'][0]['city'] ?? null,
                'state'        => $this->state['content'][0]['state'] ?? null,
                'postcode'     => $this->state['content'][0]['postcode'] ?? null,
            ],
            'items'  => [],
            'totals' => [
                'quantity' => $this->state['content'][0]['quantity'] ?? 0,
                'subtotal' => 0,
                'tax'      => $this->state['content'][0]['tax'] ?? 0,
                'discount' => $this->state['content'][0]['discount'] ?? 0,
                'total'    => $this->state['content'][0]['total'] ?? 0,
            ],
            'company' => [
                'name'    => env('APP_NAME'),
                'address' => 'Rua Rio de Janeiro, 25 - Rio de Janeiro, RJ',
                'cnpj'    => '54012200000441/4000',
                'phone'   => '(21) 966134366',
                'email'   => 'sac@codhous.app',
                'website' => 'www.codhous.app',
            ],
            'footer' => [
                'note'     => 'Este documento é apenas um orçamento e não possui valor fiscal.',
                'validity' => 'Orçamento válido por 15 dias a partir da data de emissão.',
                'payment'  => 'A combinar',
            ],
            'meta' => [
                'currency'       => env('CURRENCY_SUFFIX', 'R$'),
                'date_formatted' => date('d/m/Y H:i', strtotime($this->state['created_at'])),
                'current_date'   => date('d/m/Y'),
            ],
        ];

        // Preparar os itens do pedido
        if (isset($this->state['content'][0]['products']) && ! empty($this->state['content'][0]['products'])) {
            try {
                foreach ($this->state['content'][0]['products'] as $productGroup) {
                    foreach ($productGroup as $products) {
                        foreach ($products as $product) {
                            if (is_array($product) && isset($product['product'])) {
                                // Buscar detalhes completos do produto no banco de dados
                                $productDetails = Product::find($product['product']);
                                $productOption = null;

                                // Buscar opção do produto se disponível
                                if (isset($product['product_option']) && $product['product_option']) {
                                    $productOption = \App\Models\ProductOption::find($product['product_option']);
                                }

                                // Buscar localização se disponível
                                $location = null;
                                if (isset($product['location']) && $product['location']) {
                                    $location = \App\Models\Location::find($product['location']);
                                }

                                // Adicionar ao array de itens
                                $details['items'][] = [
                                    'product_id'   => $product['product'] ?? null,
                                    'product_name' => $productDetails->name ?? $productName?->name ?? 'Produto',
                                    'option'       => $productOption?->name ?? null,
                                    'option_id'    => $product['product_option'] ?? null,
                                    'location'     => $location?->name ?? null,
                                    'location_id'  => $product['location'] ?? null,
                                    'quantity'     => $product['quantity'] ?? 0,
                                    'price'        => $product['price'] ?? 0,
                                    'subtotal'     => $product['subtotal'] ?? 0,
                                ];

                                // Adicionar ao subtotal total
                                $details['totals']['subtotal'] += (float) ($product['subtotal'] ?? 0);
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                // Em caso de erro, manter o array de itens vazio
            }
        }

        return $details;
    }

    public function getProductName()
    {
        try {
            // Verificar a estrutura do state e tentar localizar o produto
            $content = $this->state['content'] ?? null;

            // Se content não estiver em formato de array com índice 0
            if (is_array($content) && ! isset($content[0]) && isset($content['products'])) {
                // Tentar acessar diretamente da estrutura original
                if (isset($content['products'][0]['product'])) {
                    $productId = $content['products'][0]['product'];
                } else {
                    // Caso seja uma estrutura mais complexa (produtos aninhados)
                    $products = $content['products'];
                    if (is_array($products)) {
                        foreach ($products as $group) {
                            if (is_array($group) && isset($group[0])) {
                                $productId = $group[0]['product'] ?? null;
                                if ($productId) {
                                    break;
                                }
                            }
                        }
                    }
                }
            }
            // Se content estiver no formato de array com índice 0
            elseif (isset($content[0]['products'])) {
                // Primeiro, tentar formato esperado content[0]['products'][0][0][0]['product']
                if (isset($content[0]['products'][0][0][0]['product'])) {
                    $productId = $content[0]['products'][0][0][0]['product'];
                }
                // Senão, tentar formato mais simples content[0]['products'][0]['product']
                elseif (isset($content[0]['products'][0]['product'])) {
                    $productId = $content[0]['products'][0]['product'];
                }
                // Tentar outras estruturas possíveis
                else {
                    $products = $content[0]['products'];
                    foreach ($products as $product) {
                        if (is_array($product) && isset($product['product'])) {
                            $productId = $product['product'];
                            break;
                        }
                    }
                }
            }

            // Se encontrou um ID de produto, buscar o nome
            if (isset($productId) && $productId) {
                return Product::select('name')
                    ->where('id', '=', $productId)
                    ->first();
            }
        } catch (\Exception $e) {
            \Log::error('Error getting product name: '.$e->getMessage());

            // Falhar silenciosamente se a estrutura não for como esperado
            return null;
        }

        return null;
    }

    /**
     * Summary of savePath
     * @return string
     */
    public function savePath(): string
    {
        return storage_path(env('PDF_DOWNLOAD_PATH').$this->generateFilename());
    }

    public function downloadPdf()
    {
        if ($this->filePath && file_exists($this->filePath)) {
            return response()->download($this->filePath);
        }
    }
}
