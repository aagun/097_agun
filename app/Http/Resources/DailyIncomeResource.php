<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DailyIncomeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "transaction_date" => $this->transaction_date,
            "total_income" => $this->total_income,
            "total_user_debts" => $this->total_user_debts,
            "total_transactions" => $this->total_transactions
        ];
    }
}
