<?php

namespace Ars\Cashier\Models\Traits;

use Ars\Cashier\Models\Payment;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Facades\DB;

/**
 * Trait HasPay
 *
 * Provides payment-related functionality to models, such as initiating payments,
 * processing payment results, and managing transactions associated with payments.
 *
 * @package Ars\Cashier\Models\Traits
 *
 * @property-read Collection|Payment[] $payments
 * @property-read Collection|Transaction[] $transaction
 */
trait HasPay
{
    use HasTransaction;

    /**
     * Defines a morph-one relationship to the Payment model.
     *
     * @return MorphOne
     */
    public function payments(): MorphOne
    {
        return $this->morphOne(Payment::class, 'paymentable');
    }

    /**
     * Finds a payment by its authority and loads the associated transaction.
     *
     * @param  string  $authority
     * @return Payment|null
     */
    public function findAuthority(string $authority): ?Payment
    {
        return $this->payments()->where('authority', $authority)->with('transaction')->first();
    }

    /**
     * Initiates a payment process and creates an associated transaction.
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

            // Create a related transaction for this payment
            $payment->transaction()->create([
                'user_id'  => auth()->id(),
                'amount'   => $amount,
                'accepted' => false, // Initially set as not accepted
                'meta'     => $meta,
                'type'     => 'deposit',
            ]);

            return $payment;
        });
    }

    /**
     * Processes a successful payment, updating the payment and its transaction.
     *
     * @param  string  $authority
     * @param  string|null  $statusCode
     * @param  string|null  $refId
     * @return Payment|null
     */
    public function resultSuccessPay(string $authority, ?string $statusCode = null, ?string $refId = null): ?Payment
    {
        return DB::transaction(function () use ($authority, $statusCode, $refId) {
            // Find and update the payment details
            $payment = $this->findAuthority($authority);

            if ($payment) {
                $payment->update([
                    'ref_id'      => $refId,
                    'status_code' => $statusCode,
                    'payed_at'    => now(),
                ]);

                // Mark the transaction as accepted
                $payment->transaction->update(['accepted' => true]);
            }

            return $payment;
        });
    }

    /**
     * Processes a failed payment, updating the payment and its transaction.
     *
     * @param  string  $authority
     * @param  string|null  $statusCode
     * @param  string|null  $message
     * @return Payment|null
     */
    public function resultFailedPay(string $authority, ?string $statusCode = null, ?string $message = null): ?Payment
    {
        return DB::transaction(function () use ($authority, $statusCode, $message) {
            // Find and update the payment details
            $payment = $this->findAuthority($authority);

            if ($payment) {
                $payment->update([
                    'status_code' => $statusCode,
                ]);

                // Add failure message to the transaction's metadata if provided
                if ($message) {
                    $payment->transaction->addMeta(['message' => $message]);
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
