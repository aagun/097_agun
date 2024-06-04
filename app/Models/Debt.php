<?php

namespace App\Models;

use App\Enums\DebtType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Debt extends Model
{
    use HasUuids, HasFactory, SoftDeletes;

    public $incrementing = false;
    protected $table = 'debts';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    protected $fillable = [
        'debt_type',
        'user_id',
        'total_debt',
        'remaining_debt'
    ];

    protected $with = ['user', 'detailInstallments', 'detailTransactions'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function detailInstallments(): HasMany
    {
        return $this->hasMany(Installment::class, 'debt_id', 'id');
    }

    public function detailTransactions(): BelongsToMany
    {
        return $this->belongsToMany(Transaction::class, 'detail_transactions', 'debt_id', 'transaction_id')
            ->using(DetailTransaction::class)
            ->wherePivot('deleted_at', null)
            ->withPivot('transaction_id', 'debt_id')
            ->withTimestamps();
    }

    protected function casts(): array
    {
        return [
            'debt_type' => DebtType::class
        ];
    }


}
