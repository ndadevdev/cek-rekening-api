<?php

namespace App\Services\BankValidators;

class DummyValidator implements BankValidatorInterface
{
    protected array $dummyAccounts = [
        'bca' => [
            '0888123456' => 'Budi Santoso',
            '0888987654' => 'Siti Rahmawati',
        ],
        'mandiri' => [
            '1230001234567' => 'Ahmad Hidayat',
            '1230007654321' => 'Dewi Lestari',
        ],
        'bni' => [
            '0123456789' => 'Rudi Hartono',
            '0987654321' => 'Maya Anggraini',
        ],
        'bri' => [
            '002301234567' => 'Hendra Gunawan',
            '002309876543' => 'Rina Marlina',
        ],
        'gopay' => [
            '08123456789' => 'Dimas Prayoga',
            '08987654321' => 'Putri Wulandari',
        ],
        'ovo' => [
            '087812345678' => 'Fajar Nugroho',
            '087887654321' => 'Indah Permata Sari',
        ],
        'dana' => [
            '081234567890' => 'Agus Prasetyo',
            '089876543210' => 'Ratna Kusuma',
        ],
    ];

    public function isEnabled(): bool
    {
        return true;
    }

    public function cekRekening(string $bankCode, string $accountNumber): array
    {
        $accounts = $this->dummyAccounts[$bankCode] ?? [];
        $nama = $accounts[$accountNumber] ?? null;

        if ($nama) {
            return [
                'status' => 'valid',
                'bank_code' => $bankCode,
                'account_number' => $accountNumber,
                'account_name' => $nama,
                'validator' => 'dummy',
            ];
        }

        return [
            'status' => 'invalid',
            'bank_code' => $bankCode,
            'account_number' => $accountNumber,
            'message' => 'Nomor rekening tidak ditemukan',
            'validator' => 'dummy',
        ];
    }
}
