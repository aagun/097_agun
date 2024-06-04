<?php

namespace App\Services;

use App\Enums\PaymentStatus;

interface InstallmentService
{
    public function existsByDebtIdAndInstallmentNumberAndStatus(
        string $debt_id,
        int $installment_number,
        PaymentStatus $status): bool;

    public function existsByDebtIdAndStatus(string $debt_id, PaymentStatus $status): bool;
}
