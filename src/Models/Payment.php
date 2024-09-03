<?php

namespace Ars\Cashier\Models;

use Ars\Cashier\Models\Traits\HasTransaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Payment extends Model
{

    use HasTransaction;

    /**
     * Payment constructor.
     *
     * @param  array  $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('cashier.tables.payment', 'payments'));
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'amount',
        'authority',
        'ref_id',
        'status_code',
        'gateway',
        'payed_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'payed_at' => 'datetime',
        'amount'   => 'float',
    ];

    public function paymentable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Determine if the payment has been successfully processed.
     *
     * @return bool
     */
    public function isPayed(): bool
    {
        return !is_null($this->payed_at);
    }
}
