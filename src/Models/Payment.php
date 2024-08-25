<?php

namespace Ars\Cashier\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('cashier.tables.payments', 'payments'));
    }

    public function transactions()
    {
        return $this->morphToMany(Transaction::class, 'transactionable');
    }
}
