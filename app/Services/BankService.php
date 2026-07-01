<?php

namespace App\Services;

class BankService
{
    public array $banks = [
        'bca' => ['name' => 'Bank BCA', 'type' => 'bank'],
        'bni' => ['name' => 'Bank BNI', 'type' => 'bank'],
        'mandiri' => ['name' => 'Bank Mandiri', 'type' => 'bank'],
        'bri' => ['name' => 'Bank BRI', 'type' => 'bank'],
        'cimb' => ['name' => 'CIMB Niaga', 'type' => 'bank'],
        'danamon' => ['name' => 'Bank Danamon', 'type' => 'bank'],
        'permata' => ['name' => 'Bank Permata', 'type' => 'bank'],
        'maybank' => ['name' => 'Maybank Indonesia', 'type' => 'bank'],
        'btn' => ['name' => 'Bank BTN', 'type' => 'bank'],
        'mega' => ['name' => 'Bank Mega', 'type' => 'bank'],
        'bsi' => ['name' => 'Bank Syariah Indonesia', 'type' => 'bank'],
        'ocbc' => ['name' => 'OCBC NISP', 'type' => 'bank'],
        'gopay' => ['name' => 'GoPay', 'type' => 'ewallet'],
        'ovo' => ['name' => 'OVO', 'type' => 'ewallet'],
        'dana' => ['name' => 'DANA', 'type' => 'ewallet'],
        'linkaja' => ['name' => 'LinkAja', 'type' => 'ewallet'],
        'shopeepay' => ['name' => 'ShopeePay', 'type' => 'ewallet'],
    ];

    public function getAllBanks(): array
    {
        return $this->banks;
    }

    public function findBank(string $code): ?array
    {
        $code = strtolower($code);
        return $this->banks[$code] ?? null;
    }
}
