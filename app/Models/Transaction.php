<?php

namespace App\Models;

use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Transaction extends Model
{
    use HasUuids, HasFactory;

    protected $table = 'transactions';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = [
        'user_id',
        'employee_id',
        'transaction_date',
        'transaction_type',
        'description',
        'installment_number',
        'total_installments',
        'transaction_amount',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id', 'id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id', 'id');
    }

    public function detailDebts(): BelongsToMany
    {
        return $this->belongsToMany(Debt::class, 'detail_transactions', 'transaction_id', 'debt_id')
            ->using(DetailTransaction::class);
    }

    protected function casts(): array
    {
        return [
            'transaction_type' => TransactionType::class
        ];
    }


}
