<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class DetailTransaction extends Pivot
{
    use SoftDeletes;

    protected $table = 'detail_transactions';
    protected $foreignKey = 'transaction_id';
    protected $relatedKey = 'debt_id';

    public $incrementing = false;

    public $timestamps = true;

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'transaction_id', 'id');
    }

    public function debt(): BelongsTo
    {
        return $this->belongsTo(Debt::class, 'debt_id', 'id');
    }
}
