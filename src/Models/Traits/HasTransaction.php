<?php

namespace Ars\Cashier\Models\Traits;

use Ars\Cashier\Models\Payment;
use Ars\Cashier\Models\Transaction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * Trait HasTransaction
 *
 * Provides payment-related functionality to models, such as managing
 * payments, transactions, and handling payment processes.
 *
 * @package Ars\Cashier\Models\Traits
 *
 * @property-read Collection|Payment[] $payments
 * @property-read Collection|Transaction[] $transactions
 * @property-read Transaction $transaction
 */
trait HasTransaction
{
    /**
     * Get many transactions.
     *
     * @return MorphMany
     */
    public function transactions(): MorphMany
    {
        return $this->morphMany(Transaction::class, 'transactionable');
    }

    /**
     * Get a single transaction.
     *
     * @return MorphOne
     */
    public function transaction(): MorphOne
    {
        return $this->morphOne(Transaction::class, 'transactionable');
    }

    /**
     * Add or update metadata in the associated transaction.
     * If there are multiple transactions, the first one is updated.
     *
     * @param  array  $newMeta
     * @return $this
     */
    public function addMeta(array $newMeta)
    {
        $transaction = $this->getFirstTransaction();
        if ($transaction) {
            $transaction->addMeta($newMeta);
        }

        return $this;
    }

    /**
     * Reset the metadata in the associated transaction.
     * If there are multiple transactions, the first one is reset.
     *
     * @return $this
     */
    public function resetMeta()
    {
        $transaction = $this->getFirstTransaction();
        if ($transaction) {
            $transaction->resetMeta();
        }

        return $this;
    }

    /**
     * Get the metadata in the associated transaction.
     * If there are multiple transactions, the first one is used.
     *
     * @param  string|null  $key
     * @return mixed
     */
    public function getMeta(?string $key = null)
    {
        $transaction = $this->getFirstTransaction();
        if ($transaction) {
            $meta = $transaction->meta;
            if (!is_null($key)) {
                return $meta[$key] ?? null;
            }

            return $meta;
        }

        return null;
    }

    /**
     * Get the first associated transaction.
     *
     * @return Transaction|null
     */
    protected function getFirstTransaction(): ?Transaction
    {
        return $this->transaction ?? $this->transactions()->first();
    }
}
