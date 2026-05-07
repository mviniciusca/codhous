<?php

namespace App\Enums;

enum FakeDataEnum: string
{
    case CUSTOMER_NAME = 'Codhous Software';
    case CUSTOMER_EMAIL = 'codhous@teste.ia';
    case CUSTOMER_PHONE = '(00)00000-0000';
    case POSTCODE = '00000-000';
    case STREET = 'Rua Codhous Software';
    case NUMBER = '0';
    case CITY = 'Cidade Codhous';
    case NEIGHBORHOOD = 'Bairro Codhous';
    case STATE = 'RJ';

    public static function asArray(): array
    {
        return [
            'customer_name' => self::CUSTOMER_NAME->value,
            'customer_email' => self::CUSTOMER_EMAIL->value,
            'customer_phone' => self::CUSTOMER_PHONE->value,
            'postcode' => self::POSTCODE->value,
            'street' => self::STREET->value,
            'number' => self::NUMBER->value,
            'city' => self::CITY->value,
            'neighborhood' => self::NEIGHBORHOOD->value,
            'state' => self::STATE->value,
        ];
    }

    public static function matches(array $data): bool
    {
        $fakeData = self::asArray();
        
        foreach ($fakeData as $key => $value) {
            $currentValue = $data[$key] ?? '';
            
            // Ignore formatting for phone and postcode during comparison
            if (in_array($key, ['customer_phone', 'postcode'])) {
                if (preg_replace('/[^0-9]/', '', (string)$currentValue) !== preg_replace('/[^0-9]/', '', (string)$value)) {
                    return false;
                }
                continue;
            }

            if ((string)$currentValue !== (string)$value) {
                return false;
            }
        }
        
        return true;
    }
}
