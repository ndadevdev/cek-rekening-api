<?php

namespace App\Services\BankValidators;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FlipValidator implements BankValidatorInterface
{
    protected string $apiKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.flip.api_key');
        $this->baseUrl = config('services.flip.base_url', 'https://api.flip.id');
    }

    public function isEnabled(): bool
    {
        return config('services.flip.enabled') && !empty($this->apiKey);
    }

    public function cekRekening(string $bankCode, string $accountNumber): array
    {
        $mappedBankCode = $this->mapBankCode($bankCode);

        if (!$mappedBankCode) {
            return [
                'status' => 'unsupported',
                'message' => 'Bank/ewallet tidak didukung oleh Flip',
                'validator' => 'flip',
            ];
        }

        try {
            $response = Http::withBasicAuth($this->apiKey, '')
                ->asForm()
                ->post($this->baseUrl . '/v2/disbursement/bank-account-inquiry', [
                    'bank_code' => $mappedBankCode,
                    'account_number' => $accountNumber,
                ]);

            if ($response->successful()) {
                $body = $response->json();
                return [
                    'status' => 'valid',
                    'bank_code' => $bankCode,
                    'bank_name' => $body['bank_code'] ?? '',
                    'account_number' => $accountNumber,
                    'account_name' => $body['account_holder_name'] ?? '',
                    'validator' => 'flip',
                ];
            }

            $error = $response->json();
            Log::error('Flip API error', ['status' => $response->status(), 'body' => $error]);

            return [
                'status' => 'error',
                'message' => 'Gagal memvalidasi rekening: ' . ($error['message'] ?? 'Unknown error'),
                'validator' => 'flip',
            ];
        } catch (\Exception $e) {
            Log::error('Flip connection error', ['message' => $e->getMessage()]);
            return [
                'status' => 'error',
                'message' => 'Gagal terhubung ke Flip',
                'validator' => 'flip',
            ];
        }
    }

    protected function mapBankCode(string $code): ?string
    {
        $map = [
            'bca' => 'bca', 'bni' => 'bni', 'mandiri' => 'mandiri',
            'bri' => 'bri', 'cimb' => 'cimb', 'danamon' => 'danamon',
            'permata' => 'permata', 'maybank' => 'maybank', 'btn' => 'btn',
            'mega' => 'mega', 'bsi' => 'bsi', 'ocbc' => 'ocbc_nisp',
        ];
        return $map[$code] ?? null;
    }
}
