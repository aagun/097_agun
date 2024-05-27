<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Installment extends Model
{
    use HasUuids, HasFactory;

    protected $table = 'installments';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = 'false';

    protected $fillable = [
        'installments_number',
        'amount',
        'due_date',
        'status',
        'debt_id'
    ];

    public function debt(): BelongsTo
    {
        return $this->belongsTo(Debt::class, 'debt_id', 'id');
    }

    protected function casts(): array
    {
        return [
            'status' => PaymentStatus::class
        ];
    }

}
