<?php

namespace Ars\Cashier\Models\Traits;

use Ars\Cashier\Models\Transaction;
use Ars\Cashier\Models\Wallet;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Trait HasWallet
 *
 * Provides wallet-related functionality to models, such as managing balance,
 * transactions, and payments.
 *
 * @package Ars\Cashier\Models\Traits
 *
 * @property-read float $balance
 * @property-read Wallet $wallet
 */
trait HasWallet
{
    /**
     * Accessor for the balance attribute.
     *
     * @return float
     */
    public function getBalanceAttribute(): float
    {
        return $this->wallet->balance ?? 0.0;
    }

    /**
     * Relationship to the user's transactions.
     *
     * @return HasMany
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Relationship to the user's wallet.
     *
     * @return HasOne
     */
    public function wallet(): HasOne
    {
        return $this->hasOne(Wallet::class)->withDefault();
    }

    /**
     * Check if the user can withdraw a certain amount, considering the ceiling.
     *
     * @param float $amount
     * @param float $ceiling
     * @return bool
     */
    public function canWithdraw(float $amount, float $ceiling): bool
    {
        return ($this->balance + $ceiling) >= $amount;
    }

    /**
     * Withdraws an amount from the user's wallet and logs the transaction.
     *
     * @param float $amount
     * @param array $meta
     * @return Model
     */
    public function withdraw(float $amount, array $meta = []): Model
    {
        $wallet = $this->wallet;
        $accepted = $this->canWithdraw($amount, $wallet->ceilingWithdraw);

        if ($accepted) {
            $wallet->decrement('balance', $amount);
        }

        return $wallet->transactions()->create([
            'user_id'  => auth()->id(),
            'amount'   => $amount,
            'accepted' => $accepted,
            'meta'     => $meta,
            'type'     => 'withdraw',
        ]);
    }

    /**
     * Deposits an amount into the user's wallet and logs the transaction.
     *
     * @param float $amount
     * @param array $meta
     * @return Model
     */
    public function deposit(float $amount, array $meta = []): Model
    {
        $wallet = $this->wallet;

        $wallet->increment('balance', $amount);

        return $wallet->transactions()->create([
            'user_id'  => auth()->id(),
            'amount'   => $amount,
            'accepted' => true,
            'meta'     => $meta,
            'type'     => 'deposit',
        ]);
    }
}