<?php

namespace App\Services\Impl;

use App\Services\TransactionService;
use App\Models\Transaction;
use App\Enums\TransactionType;
use App\Models\Debt;
use Illuminate\Database\Query\JoinClause;
use App\Http\Requests\PageableRequest;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class TransactionServiceImpl implements TransactionService
{
    public function sumTransactionAmountByTransactionType(TransactionType $transaction_type): int
    {
        return Transaction::query()
            ->whereRaw('DATE(transaction_date) = CURRENT_DATE')
            ->where('transaction_type', $transaction_type)
            ->sum('transaction_amount');
    }

    public function monthlyIncome(): array | Collection
    {
        return Transaction::query()
            ->where('transaction_type', '=', TransactionType::PAY_DEBT)
            ->selectRaw('(MONTH(transaction_date) - 1) AS month_index, SUM(transaction_date) AS monthly_income')
            ->groupByRaw('month_index')
            ->orderBy('month_index')
            ->get();
    }

    public function searchTransaction(PageableRequest $request): LengthAwarePaginator
    {


        return Transaction::query()
            ->when()
            ->paginate();
    }

    public function searchTransactionPreparePageable(PageableRequest $request): PageableRequest
    {

    }

    public function dailyIncome(PageableRequest $request): LengthAwarePaginator
    {

        $filter = $this->dailyIncomePreparePageable($request);

        $user_debts =Debt::query()
            ->selectRaw('COUNT(DISTINCT user_id) AS total, user_id')
            ->groupBy('user_id');

        return Transaction::query()
            ->selectRaw('
                    DATE(transaction_date) AS transaction_date,
                    SUM(transaction_amount) AS total_income,
                    user_debts.total as total_user_debts,
                    COUNT(DATE(transaction_date)) total_transactions')

            ->joinSub($user_debts, 'user_debts', function (JoinClause $join) {
                $join->on(
                    'transactions.user_id',
                    '=',
                    'user_debts.user_id'
                );
            })
            ->where('transaction_type', TransactionType::PAY_DEBT)
            ->when($filter->search, function(Builder $query, $filter) {
                $query->where(function ($query) use ($filter) {
                    $this->dailyIncomeFilter($query, $filter);
                });
            })
            ->groupByRaw('DATE(transaction_date), total_user_debts')
            ->orderByRaw("$filter->sort $filter->order")
            ->paginate(perPage: $filter->limit, page: $filter->offset);
    }

    private function dailyIncomeFilter(Builder $query, $filter): void
    {
        if ($filter['transaction_date']) {
            $query->whereRaw('transaction_date = DATE(?)', [$filter['transaction_date']]);
        }

        if ($filter['total_income']) {
            $query->whereRaw('total_income = ?', [$filter['total_income']]);
        }

    }

    private function dailyIncomePreparePageable(PageableRequest $request): PageableRequest
    {
        $permissible_sort = collect(['total_income', 'total_user_debts', 'total_transactions']);
        $sort = $permissible_sort->contains(strtolower($request->sort)) ? $request->sort : 'DATE(transaction_date)';
        $order = collect(['desc', 'asc'])->contains(strtolower($request->order)) ? $request->order : 'desc';
        $limit = $request->limit ?? 10;
        $offset = $request->offset ?? 1;
        $search = $request->search ?? [];

        return new PageableRequest([
            'search' => $search,
            'sort' => $sort,
            'order' => $order,
            'limit' => $limit,
            'offset' => $offset
        ]);
    }
}
