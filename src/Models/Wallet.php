<?php

namespace Ars\Cashier\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Wallet
 * @package Ars\Cashier\Models
 *
 * @property float $balance
 * @property int $ceiling_withdraw
 * @property string $uuid
 */
class Wallet extends Model
{
    /**
     * The default withdrawal ceiling, fetched from the config.
     *
     * @var int
     */
    protected $ceilingWithdraw;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('cashier.tables.wallets', 'wallets'));
        $this->ceilingWithdraw = config('cashier.ceiling_withdraw', 0);
    }

    /**
     * Get all transactions for the wallet.
     *
     * @return MorphMany
     */
    public function transactions(): MorphMany
    {
        return $this->morphMany(Transaction::class, 'transactionable');
    }

    /**
     * Get the user who owns the wallet.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}