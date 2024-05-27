<?php

namespace App\Models;

use App\Enums\DebtType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Debt extends Model
{
    use HasUuids, HasFactory;

    protected $table = 'debts';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'debt_type',
        'user_id'
    ];

    protected $with = ['user'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function installments(): HasMany
    {
        return $this->hasMany(Installment::class, 'debt_id', 'id');
    }

    protected function casts(): array
    {
        return [
            'debt_type' => DebtType::class
        ];
    }


}
