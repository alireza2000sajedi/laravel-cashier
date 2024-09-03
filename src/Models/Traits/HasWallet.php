<?php

namespace Ars\Cashier\Models\Traits;

use Ars\Cashier\Models\Transaction;
use Ars\Cashier\Models\Wallet;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * Trait HasWallet
 *
 * Provides wallet-related functionality to models, such as managing balance,
 * transactions, and payments.
 *
 * @package Ars\Cashier\Models\Traits
 *
 * @property-read float|int $balance
 * @property-read Wallet $wallet
 */
trait HasWallet
{

    use HasTransaction;

    /**
     * Determine if transactions should be stored for wallet.
     *
     * @return bool
     */
    protected function shouldStoreTransactionWallet(): bool
    {
        return true;
    }

    /**
     * Accessor for the balance attribute.
     *
     * @return float|int
     */
    public function getBalanceAttribute(): float|int
    {
        return $this->wallet->balance ?? 0;
    }

    /**
     * Relationship to the user's wallet.
     *
     * @return MorphOne
     */
    public function wallet(): MorphOne
    {
        return $this->morphOne(Wallet::class, 'walletable')->withDefault();
    }

    /**
     * Check if the user can withdraw a certain amount, considering the ceiling.
     *
     * @param  float|int  $amount
     * @param  float|int  $ceiling
     * @return bool
     */
    public function canWithdraw(float|int $amount, float|int $ceiling): bool
    {
        return ($this->balance + $ceiling) >= $amount;
    }

    /**
     * Withdraws an amount from the user's wallet and logs the transaction.
     *
     * @param  float|int  $amount
     * @param  array  $meta
     * @return Model
     */
    public function withdraw(float|int $amount, array $meta = []): Model
    {
        $wallet = $this->wallet;
        $accepted = $this->canWithdraw($amount, $wallet->ceilingWithdraw);

        if ($accepted) {
            $wallet->balance -= $amount;
        }
        $wallet->save();

        // Log the transaction if enabled
        if ($this->shouldStoreTransactionWallet()) {
            return $wallet->transactions()->create([
                'user_id'  => auth()->id(),
                'amount'   => $amount,
                'accepted' => $accepted,
                'meta'     => $meta,
                'type'     => 'withdraw',
            ]);
        }

        return $wallet;
    }

    /**
     * Deposits an amount into the user's wallet and logs the transaction.
     *
     * @param  float|int  $amount
     * @param  array  $meta
     * @return Model
     */
    public function deposit(float|int $amount, array $meta = []): Model
    {
        $wallet = $this->wallet;

        $wallet->balance += $amount;
        $wallet->save();

        // Log the transaction if enabled
        if ($this->shouldStoreTransactionWallet()) {
            return $wallet->transactions()->create([
                'user_id'  => auth()->id(),
                'amount'   => $amount,
                'accepted' => true,
                'meta'     => $meta,
                'type'     => 'deposit',
            ]);
        }

        return $wallet;
    }
}
