<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use App\Enums\TransactionType;
use App\Enums\DebtType;
use App\Services\DebtService;
use App\Services\InstallmentService;
use App\Enums\PaymentStatus;
use App\Models\Installment;

class TransactionBatchRequest extends BaseRequest
{
    private DebtService $debtService;
    private InstallmentService $installmentService;

    public function __construct(DebtService $debtService, InstallmentService $installmentService)
    {
        parent::__construct();
        $this->debtService = $debtService;
        $this->installmentService = $installmentService;
    }


    public function rules(): array
    {
        return [
            "*.user_id" => [
                'required',
                'string',
                Rule::exists('users', 'id')
            ],
            "*.transaction_type" => [
                'required',
                Rule::enum(TransactionType::class)
            ],
            "*.description" => ['required', 'string'],
            "*.transaction_amount" => ['required', 'integer', 'min:10000'],
            "*.installment_number" => ['nullable', 'integer'],
            "*.debt_id" => ['nullable', 'string'],
            "*.total_installments" => ['nullable', 'integer'],
            "*.debt_type" => [
                'required',
                Rule::enum(DebtType::class)
            ]
        ];
    }

    protected function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $data = $this->all();
            foreach ($data as $index => $transaction) {
                if (!isset($transaction[ 'transaction_type' ])) {
                    continue;
                }

                if (toEnum($transaction[ 'transaction_type' ], TransactionType::class) === TransactionType::NEW_DEBT) {
                    $this->validateMandatoryForNewDebt($validator, $index, $transaction);
                } else if (toEnum($transaction[ 'transaction_type' ], TransactionType::class) === TransactionType::PAY_DEBT) {
                    $this->validateMandatoryForPayDebt($validator, $index, $transaction);
                }
            }
        });
    }

    private function validateMandatoryForNewDebt($validator, $index, $transaction): void
    {
        if (toEnum($transaction[ 'debt_type' ], DebtType::class) === DebtType::MONTHLY) {
            if (!isset($transaction[ 'total_installments' ])) {
                $validator->errors()
                    ->add(
                        "$index.total_installments",
                        trans(
                            'validation.custom.transaction_payload.required_newdebt',
                            [
                                'attribute' => "$index.total_installments",
                                'transaction_type' => TransactionType::NEW_DEBT->name,
                                'debt_type' => DebtType::MONTHLY->name
                            ]
                        ),
                    );
            } else if ($transaction[ 'total_installments' ] < 1) {
                $validator->errors()
                    ->add(
                        "$index.total_installments",
                        trans(
                            'validation.custom.transaction_payload.min_newdebt',
                            [
                                'attribute' => "$index.total_installments",
                                'min' => 1,
                                'transaction_type' => TransactionType::NEW_DEBT->name,
                                'debt_type' => DebtType::MONTHLY->name
                            ]
                        ),
                    );
            }
        }
    }

    private function validateMandatoryForPayDebt($validator, $index, $transaction): void
    {
        if (!isset($transaction[ 'debt_id' ])) {
            $validator->errors()
                ->add(
                    "$index.debt_id",
                    trans(
                        "validation.custom.transaction_payload.required_paydebt",
                        [
                            'attribute' => "$index.debt_id",
                            'transaction_type' => TransactionType::PAY_DEBT->name
                        ]
                    ),
                );
        } else if (is_string($transaction[ 'debt_id' ])
            && !$this->debtService->exists($transaction[ 'debt_id' ])) {
            $validator->errors()
                ->add(
                    "$index.debt_id",
                    trans(
                        "validation.exists",
                        [
                            'attribute' => "$index.debt_id",
                        ]
                    ),
                );
        } else if (is_String($transaction[ 'debt_id' ])
            && !$this->debtService->existByIdAndDebtType(
                $transaction[ 'debt_id' ],
                toEnum($transaction[ 'debt_type' ], DebtType::class
                )
            )) {
            $validator->errors()
                ->add(
                    "$index.transaction",
                    trans("validation.custom.transaction"),
                );
        } else if (toEnum($transaction[ 'debt_type' ], DebtType::class) === DebtType::MONTHLY) {
            $this->validatePayDebtMonthly($validator, $index, $transaction);
        } else if (toEnum($transaction[ 'debt_type' ], DebtType::class) === DebtType::SEASON) {
            $this->validatePayDebtSeason($validator, $index, $transaction);
        }
    }

    private function validatePayDebtMonthly($validator, $index, $transaction): void
    {
        if (!isset($transaction[ 'installment_number' ])) {
            $validator->errors()
                ->add(
                    "$index.installment_number",
                    trans(
                        "validation.custom.transaction_payload.required_paydebt_debtid",
                        [
                            'attribute' => "$index.installment_number",
                            'transaction_type' => TransactionType::PAY_DEBT->name,
                            'debt_type' => DebtType::MONTHLY->name
                        ]
                    ),
                );
        } else if (is_String($transaction[ 'debt_id' ])
            && !$this->installmentService->existsByDebtIdAndInstallmentNumberAndStatus(
                $transaction[ 'debt_id' ],
                $transaction[ 'installment_number' ],
                PaymentStatus::UNPAID)) {
            $validator->errors()
                ->add(
                    "$index.installment_number",
                    trans(
                        "validation.exists",
                        [
                            'attribute' => "$index.installment_number",
                        ]
                    ),
                );
        } else if (is_string($transaction[ 'debt_id' ]) && !$this->isBalanceSufficient($transaction)) {
            $validator->errors()
                ->add(
                    "$index.transaction_amount",
                    trans(
                        "validation.custom.transaction_insufficient_balance",
                        [
                            'attribute' => "$index.transaction_amount",
                        ]
                    ),
                );
        }
    }

    private function validatePayDebtSeason($validator, $index, $transaction): void
    {
        if (!$this->installmentService->existsByDebtIdAndStatus(
            $transaction[ 'debt_id' ],
            PaymentStatus::UNPAID)
        ) {
            $validator->errors()
                ->add(
                    "$index.transaction",
                    trans("validation.custom.transaction"),
                );
        } else if (!$this->isBalanceSufficient($transaction)) {
            $validator->errors()
                ->add(
                    "$index.transaction_amount",
                    trans(
                        "validation.custom.transaction_insufficient_balance",
                        [
                            'attribute' => "$index.transaction_amount",
                        ]
                    ),
                );
        }
    }

    private function isBalanceSufficient(array $payload): bool
    {
        $debt = $this->debtService->findById($payload[ 'debt_id' ]);
        $detailInstallment = $debt->detailInstallments();

        $installment = new Installment();
        if (toEnum($payload[ 'debt_type' ], DebtType::class) === DebtType::MONTHLY) {
            $installment = $detailInstallment
                ->where('installment_number', $payload[ 'installment_number' ])
                ->where('status', PaymentStatus::UNPAID)
                ->first();
        } else if (toEnum($payload[ 'debt_type' ], DebtType::class) === DebtType::SEASON) {
            $installment = $detailInstallment
                ->where('status', PaymentStatus::UNPAID)
                ->first();
        }

        return $payload[ 'transaction_amount' ] >= $installment->amount;
    }
}
