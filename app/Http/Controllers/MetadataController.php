<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UserService;
use App\Services\DebtService;
use App\Enums\TransactionType;
use App\Http\Resources\SuccessResponseResource;
use App\Services\TransactionService;
use Illuminate\Http\Response;
use App\Http\Requests\PageableRequest;
use App\Http\Resources\SuccessPageableResponseCollection;
use App\Http\Resources\DailyIncomeResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Http\Resources\FailResponseResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

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

    public function getDailyIncome(PageableRequest $request): ResourceCollection
    {
        if (isset($request->search)) {
            $rules = [
                'total_income' => ['sometimes', 'number'],
                'transaction_date' => ['sometimes', 'date', 'date_format:Y-m-d']
            ];

            $validator = Validator::make($request->search, $rules);

            if ($validator->fails()) {
                throw new HttpResponseException(
                    response(
                        new FailResponseResource(
                            'error',
                            $validator->getMessageBag()
                        )
                    )
                );
            }
        }

        $list_daily_income = $this->transactionService->dailyIncome($request);
        return new SuccessPageableResponseCollection(
            'metadata',
            $list_daily_income,
            DailyIncomeResource::class
        );
    }

    public function getMonthlyIncome(): Response
    {
        $monthly_income = $this->transactionService->monthlyIncome();
        return response(
            new SuccessResponseResource('metadata monthly', $monthly_income)
        );
    }
}
