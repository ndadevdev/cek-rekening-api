<?php

namespace App\Services\BankValidators;

interface BankValidatorInterface
{
    public function cekRekening(string $bankCode, string $accountNumber): array;

    public function isEnabled(): bool;
}
