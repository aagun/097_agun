<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UserService;
use App\Services\DebtService;
use App\Enums\TransactionType;
use App\Http\Resources\SuccessResponseResource;
use App\Services\TransactionService;
use Illuminate\Http\Response;

class MetadataController extends Controller
{
    private UserService $userService;

    private DebtService $debtService;

    private TransactionService $transactionService;

    public function __construct(UserService $userService, DebtService $debtService, TransactionService $transactionService)
    {
        $this->userService = $userService;
        $this->debtService = $debtService;
        $this->transactionService = $transactionService;
    }

    public function getMetadata(Request $request): Response
    {
        $total_users = $this->userService->countByRoleName('RO_USER');
        $total_debt = $this->debtService->sumRemainingDebt();
        $last_income = $this->transactionService
            ->sumTransactionAmountByTransactionType(TransactionType::PAY_DEBT);

        return response(
            new SuccessResponseResource(
                'Metadata',
                [
                    'total_users' => $total_users,
                    'total_debt' => $total_debt,
                    'last_income' => $last_income
                ]
            )
        );
    }
}
