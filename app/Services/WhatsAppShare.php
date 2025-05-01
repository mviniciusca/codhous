<?php

namespace App\Services;

class WhatsAppShare
{
    public function generateUrl(string $phone, string $message): string
    {
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        $countryCode = env('COUNTRY_CODE', '55');
        $baseUrl = env('WPP_URL', 'https://wa.me/');

        $fullPhone = $countryCode.$cleanPhone;
        $encodedMessage = urlencode($message);

        return "{$baseUrl}{$fullPhone}?text={$encodedMessage}";
    }
}
