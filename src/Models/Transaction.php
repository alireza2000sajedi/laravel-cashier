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
        'amount',
        'type',
        'user_id',
        'section',
        'accepted',
        'meta',
    ];

    protected $casts = [
        'amount' => 'float',
        'meta'   => 'json',
    ];

    public function transactionable(): MorphTo
    {
        return $this->morphTo();
    }


    /**
     * Add or update metadata in the transaction.
     *
     * @param  array  $newMeta
     * @return $this
     */
    public function addMeta(array $newMeta)
    {
        $meta = array_merge($this->meta ?? [], $newMeta);
        $this->meta = $meta;
        $this->save();

        return $this;
    }

    /**
     * Reset the metadata in the transaction.
     *
     * @return $this
     */
    public function resetMeta()
    {
        $this->meta = [];
        $this->save();

        return $this;
    }
}
