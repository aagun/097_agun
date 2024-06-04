<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionBatchRequest;
use App\Models\Transaction;
use App\Enums\TransactionType;
use App\Enums\DebtType;
use App\Models\Debt;
use App\Models\Installment;
use App\Enums\PaymentStatus;
use Illuminate\Support\Carbon;
use Illuminate\Http\Response;
use App\Services\UserService;
use App\Services\DebtService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\SuccessResponseResource;

class TransactionController extends Controller
{
    private UserService $userService;

    private DebtService $debtService;

    public function __construct(UserService $userService, DebtService $debtService)
    {
        $this->userService = $userService;
        $this->debtService = $debtService;
    }

    public function createBatchTransaction(TransactionBatchRequest $request): Response
    {
        $payload = $request->validated();
        foreach ($payload as $item) {
            $user = $this->userService->findUserById($item[ 'user_id' ]);
            $item[ 'debt_type' ] = toEnum($item[ 'debt_type' ], DebtType::class);
            $item[ 'transaction_type' ] = toEnum(($item[ 'transaction_type' ]), TransactionType::class);

            $transaction = new Transaction();
            $transaction->user_id = $item[ 'user_id' ];
            $transaction->employee_id = Auth::id();
            $transaction->transaction_amount = $item[ 'transaction_amount' ];
            $transaction->description = $item[ 'description' ];

            /*
            |-------------------------------------------------------
            | Insert data for transaction type is pay debt
            |-------------------------------------------------------
            */
            if ($this->isPayDebt($item[ 'transaction_type' ])) {
                $transaction->installment_number = $item[ 'installment_number' ];
                $transaction->save();
                $transaction->detailDebts()->attach($item[ 'debt_id' ]);

                $this->updateInstallmentPayDebt($item);
                continue;
            }

            /*
            |-------------------------------------------------------
            | Insert data for transaction type is new debt
            |-------------------------------------------------------
            */
            $flexible_debt = $this->debtService->findByUserIdAndDebtType($user->id, $item[ 'debt_type' ]);

            if (!$this->doesIncreaseFlexibleDebt($item[ 'debt_type' ], $flexible_debt)) {
                $this->saveNewDebt($item, $transaction);
                continue;
            }

            /*
            |-------------------------------------------------------
            | Increase amount of debt
            | if a debt exists and the debt type is flexible
            |-------------------------------------------------------
            */
            $flexible_debt->first()->total_debt += $item[ 'transaction_amount' ];
            $flexible_debt->first()->remaining_debt += $item[ 'transaction_amount' ];
            $flexible_debt->first()->update();

            $installments = $flexible_debt->first()->detailInstallments()->first();
            $installments->amount += $item[ 'transaction_amount' ];
            $installments->update();
        }

        return response(
            new SuccessResponseResource('The record has been successfully created.')
            , Response::HTTP_CREATED);
    }

    private function updateInstallmentPayDebt($item): void
    {
        $current_debt = $this->debtService->findById($item[ 'debt_id' ]);
        $current_debt->remaining_debt -= $item[ 'transaction_amount' ];
        $current_debt->update();

        $detailInstallments = $current_debt->detailInstallments();

        if ($item[ 'debt_type' ] === DebtType::MONTHLY) {
            $the_installment = $detailInstallments->where('installment_number', $item[ 'installment_number' ])
                ->where('status', PaymentStatus::UNPAID)->first();
            $the_installment->update(['status' => PaymentStatus::PAID]);
            $the_installment->delete();
        } else if ($item[ 'debt_type' ] === DebtType::SEASON) {
            $detailInstallments->update(['status' => PaymentStatus::PAID]);
            $detailInstallments->delete();
        } else {
            $installment = $detailInstallments->first();
            $amount = $installment->amount - $item[ 'transaction_amount' ];
            $amount = max($amount, 0);
            $status = $amount > 0 ? PaymentStatus::UNPAID : PaymentStatus::PAID;

            $detailInstallments->update([
                'status' => $status,
                'amount' => $amount
            ]);

            if ($detailInstallments->first()->amount == 0) {
                $detailInstallments->first()->delete();
            }
        }

        if ($current_debt->remaining_debt == 0) {
            $current_debt->delete();
        }
    }

    private function saveNewDebt($item, $transaction): void
    {
        // save debt
        $debt = new Debt();
        $debt->user_id = $item[ 'user_id' ];
        $debt->debt_type = $item[ 'debt_type' ];
        $debt->total_debt = $item[ 'transaction_amount' ];
        $debt->remaining_debt = $item[ 'transaction_amount' ];
        $debt->save();

        $transaction->transaction_type = $item[ 'transaction_type' ];
        $transaction->total_installments = match ($item[ 'debt_type' ]) {
            DebtType::MONTHLY => $item[ 'total_installments' ],
            default => 1,
        };

        // save installment(s)
        $debt->detailInstallments()
            ->saveMany($this->prepareInstallmentModel(
                $debt->id,
                $transaction->total_installments,
                $transaction->transaction_amount,
                $item[ 'debt_type' ]
            ));

        $transaction->save();
        $transaction->detailDebts()->attach($debt->id);
    }

    private function prepareInstallmentModel(string $debt_id, int $total_installments, int $transaction_amount, DebtType $debt_type): Collection
    {
        $installment_amount = $transaction_amount / $total_installments;
        return collect(range(1, $total_installments))
            ->map(function ($i) use ($transaction_amount, $debt_id, $installment_amount, $debt_type) {
                $installment = new Installment();
                $installment->debt_id = $debt_id;
                $installment->amount = $installment_amount;
                $installment->installment_number = $i;
                $installment->status = PaymentStatus::UNPAID;
                $installment->due_date = match ($debt_type) {
                    DebtType::SEASON => Carbon::now()->addMonths(3),
                    DebtType::MONTHLY => Carbon::now()->addMonths($i),
                    default => null
                };
                return $installment;
            });
    }

    private function isPayDebt(TransactionType $transaction_type): bool
    {
        return $transaction_type == TransactionType::PAY_DEBT;
    }

    private function doesIncreaseFlexibleDebt(DebtType $debt_type, Collection $debt): bool
    {
        return $debt_type == DebtType::FLEXIBLE && $debt->count() > 0;
    }

}
