<?php

namespace App\Services\BankValidators;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class XenditValidator implements BankValidatorInterface
{
    protected string $apiKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.xendit.api_key');
        $this->baseUrl = config('services.xendit.base_url', 'https://api.xendit.co');
    }

    public function isEnabled(): bool
    {
        return config('services.xendit.enabled') && !empty($this->apiKey);
    }

    public function cekRekening(string $bankCode, string $accountNumber): array
    {
        $mappedBankCode = $this->mapBankCode($bankCode);

        if (!$mappedBankCode) {
            return [
                'status' => 'unsupported',
                'message' => 'Bank/ewallet tidak didukung oleh Xendit',
                'validator' => 'xendit',
            ];
        }

        try {
            $response = Http::withBasicAuth($this->apiKey, '')
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($this->baseUrl . '/bank_account_validation', [
                    'bank_account_bank_id' => $mappedBankCode,
                    'bank_account_number' => $accountNumber,
                ]);

            if ($response->successful()) {
                $body = $response->json();
                return [
                    'status' => 'valid',
                    'bank_code' => $bankCode,
                    'bank_name' => $body['bank_account_bank_name'] ?? '',
                    'account_number' => $accountNumber,
                    'account_name' => $body['bank_account_holder_name'] ?? '',
                    'validator' => 'xendit',
                ];
            }

            $error = $response->json();

            if (isset($error['error_code']) && $error['error_code'] === 'BANK_ACCOUNT_NOT_FOUND') {
                return [
                    'status' => 'invalid',
                    'bank_code' => $bankCode,
                    'account_number' => $accountNumber,
                    'message' => 'Nomor rekening tidak ditemukan',
                    'validator' => 'xendit',
                ];
            }

            Log::error('Xendit API error', ['status' => $response->status(), 'body' => $error]);

            return [
                'status' => 'error',
                'message' => 'Gagal memvalidasi rekening: ' . ($error['message'] ?? 'Unknown error'),
                'validator' => 'xendit',
            ];
        } catch (\Exception $e) {
            Log::error('Xendit connection error', ['message' => $e->getMessage()]);
            return [
                'status' => 'error',
                'message' => 'Gagal terhubung ke Xendit',
                'validator' => 'xendit',
            ];
        }
    }

    protected function mapBankCode(string $code): ?string
    {
        $map = [
            'bca' => 'BCA', 'bni' => 'BNI', 'mandiri' => 'MANDIRI',
            'bri' => 'BRI', 'cimb' => 'CIMB', 'danamon' => 'DANAMON',
            'permata' => 'PERMATA', 'maybank' => 'MAYBANK', 'btn' => 'BTN',
            'mega' => 'MEGA', 'bsi' => 'BSI', 'ocbc' => 'OCBC',
        ];
        return $map[$code] ?? null;
    }
}
