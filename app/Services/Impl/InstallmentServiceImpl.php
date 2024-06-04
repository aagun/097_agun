<?php

namespace App\Services\Impl;

use App\Services\InstallmentService;
use App\Models\Installment;
use App\Enums\PaymentStatus;

class InstallmentServiceImpl implements InstallmentService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function existsByDebtIdAndInstallmentNumberAndStatus(string $debt_id, int $installment_number, PaymentStatus $status): bool
    {
        return Installment::query()
            ->where('debt_id', $debt_id)
            ->where('installment_number', $installment_number)
            ->where('status', $status)
            ->exists();
    }

    public function existsByDebtIdAndStatus(string $debt_id, PaymentStatus $status): bool
    {
        return Installment::query()
            ->where('debt_id', $debt_id)
            ->where('status', $status)
            ->exists();
    }



}
