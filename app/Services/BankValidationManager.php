<?php

namespace App\Services;

use App\Services\BankValidators\BankValidatorInterface;
use Illuminate\Support\Facades\Log;

class BankValidationManager
{
    protected array $validators = [];

    public function addValidator(BankValidatorInterface $validator): void
    {
        if ($validator->isEnabled()) {
            $this->validators[] = $validator;
        }
    }

    public function cekRekening(string $bankCode, string $accountNumber): array
    {
        foreach ($this->validators as $validator) {
            try {
                $result = $validator->cekRekening($bankCode, $accountNumber);

                if ($result['status'] === 'valid') {
                    return $result;
                }

                if ($result['status'] === 'invalid') {
                    return $result;
                }

                if ($result['status'] === 'unsupported') {
                    continue;
                }

                if ($result['status'] === 'error') {
                    Log::warning('Validator error, trying next', [
                        'validator' => $result['validator'] ?? 'unknown',
                        'message' => $result['message'] ?? '',
                    ]);
                    continue;
                }
            } catch (\Exception $e) {
                Log::warning('Validator exception, trying next', [
                    'message' => $e->getMessage(),
                ]);
                continue;
            }
        }

        return [
            'status' => 'invalid',
            'message' => 'Rekening tidak ditemukan di semua sumber data',
        ];
    }
}
