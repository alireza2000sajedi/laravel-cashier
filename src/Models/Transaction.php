<?php

namespace Ars\Cashier\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('cashier.tables.transaction', 'transactions'));
    }

    protected $fillable = [
        'amount', 'type', 'user_id', 'section', 'accepted', 'meta',
    ];

    protected $casts = [
        'amount' => 'float',
        'meta'   => 'json',
    ];

    public function transactionable(): MorphTo
    {
        return $this->morphTo();
    }
}
