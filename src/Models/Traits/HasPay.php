<?php

namespace Ars\Cashier\Models\Traits;

use Ars\Cashier\Models\Payment;
use Ars\Cashier\Models\Transaction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Facades\DB;

/**
 * Trait HasPay
 *
 * Provides payment-related functionality to models, such as managing
 * payments, transactions, and handling payment processes.
 *
 * @package Ars\Cashier\Models\Traits
 *
 * @property-read Collection|Payment[] $payments
 * @property-read Collection|Transaction[] $transaction
 */
trait HasPay
{

    /**
     * Determine if transactions should be stored for payments.
     *
     * @return bool
     */
    protected function shouldStoreTransactionPayment(): bool
    {
        return true;
    }


    /**
     * Relationship to the user's payments.
     *
     * @return MorphOne
     */
    public function payments(): MorphOne
    {
        return $this->morphOne(Payment::class, 'paymentable');
    }

    /**
     * Initiates a payment process.
     *
     * @param  string  $authority
     * @param  float|int  $amount
     * @param  array  $meta
     * @param  string|null  $refId
     * @return Payment
     */
    public function requestPay(string $authority, float|int $amount, array $meta = [], ?string $refId = null): Payment
    {
        return DB::transaction(function () use ($amount, $authority, $meta, $refId) {
            // Create the payment entry
            $payment = $this->payments()->create([
                'amount'    => $amount,
                'gateway'   => $this->getGateway(),
                'authority' => $authority,
                'ref_id'    => $refId,
            ]);

            // Log the payment initiation as a transaction if enabled
            if ($this->shouldStoreTransactionPayment()) {
                $payment->transaction()->create([
                    'user_id'  => auth()->id(),
                    'amount'   => $amount,
                    'accepted' => false, // Initially not accepted
                    'meta'     => $meta,
                    'type'     => 'deposit',
                ]);
            }

            return $payment;
        });
    }

    /**
     * Processes the result of a payment.
     *
     * @param  string  $authority
     * @param  string|null  $statusCode
     * @param  string|null  $refId
     * @return Payment|null
     */
    public function resultPay(string $authority, ?string $statusCode = null, ?string $refId = null): ?Payment
    {
        return DB::transaction(function () use ($authority, $statusCode, $refId) {
            // Update the payment details
            $payment = $this->payments()->where('authority', $authority)->first();

            if ($payment) {
                $payment->update([
                    'ref_id'      => $refId,
                    'status_code' => $statusCode,
                    'payed_at'    => now(),
                ]);

                // Update the transaction if enabled
                if ($this->shouldStoreTransactionPayment()) {
                    $payment->transaction?->update(['accepted' => true]);
                }
            }

            return $payment;
        });
    }

    /**
     * Defines the payment gateway to be used.
     *
     * @return string
     */
    public function getGateway(): string
    {
        return 'default';
    }
}
