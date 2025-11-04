<?php

namespace App\Services;

class FakeBudgetDataService
{
    /**
     * Generate fake customer data
     *
     * @return array
     */
    public function generateCustomerData(): array
    {
        return [
            'customer_name'  => env('APP_NAME', 'Codhous'),
            'customer_email' => 'provisorio@teste.dev',
            'customer_phone' => '(21)99999-9999',
        ];
    }

    /**
     * Generate fake address data
     *
     * @return array
     */
    public function generateAddressData(): array
    {
        return [
            'postcode'     => '00000-000',
            'street'       => 'Rua Provisória',
            'number'       => '0',
            'city'         => 'Cidade Teste',
            'neighborhood' => 'Bairro Teste',
            'state'        => 'RJ',
        ];
    }

    /**
     * Generate complete fake budget data
     *
     * @return array
     */
    public function generateCompleteBudgetData(): array
    {
        $customerData = $this->generateCustomerData();
        $addressData = $this->generateAddressData();

        return array_merge(
            $customerData,
            $addressData
        );
    }

    /**
     * Generate only specific section data
     *
     * @param string $section ('customer'|'address'|'all')
     * @param array $options
     * @return array
     */
    public function generateSectionData(string $section, array $options = []): array
    {
        return match ($section) {
            'customer' => $this->generateCustomerData(),
            'address'  => $this->generateAddressData(),
            'all'      => $this->generateCompleteBudgetData(),
            default    => [],
        };
    }
}
