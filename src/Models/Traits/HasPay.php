<?php


namespace Ars\Cashier\Models\Traits;

use Ars\Cashier\Models\Payment;
use Ars\Cashier\Models\Transaction;
use Ars\Cashier\Models\Wallet;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class User
 * @package App\Models
 *
 * @property $balance
 * @property Wallet $wallet
 */
trait HasPay
{
    public function getBalanceAttribute(): float
    {
        return $this->wallet->balance ?? 0;
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function wallet(): HasOne
    {
        return $this->hasOne(Wallet::class)->withDefault();
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function canWithdraw(float $amount, float $ceiling): bool
    {
        return $this->balance + $ceiling >= $amount;
    }

    public function withdraw(int $amount, array $meta = []): Model
    {
        $accepted = $this->canWithdraw($amount, $this->wallet->ceiling_withdraw);

        if ($accepted) {
            $this->wallet->balance -= $amount;
            $this->wallet->save();
        } else {
            $this->wallet->save();
        }

        return $this->wallet->transactions()->create([
            'user_id'  => auth()->id(),
            'amount'   => $amount,
            'accepted' => $accepted,
            'meta'     => $meta,
            'type'     => __FUNCTION__,
        ]);
    }

    public function deposit(float $amount, array $meta = []): Model
    {
        $this->wallet->balance += $amount;
        $this->wallet->save();

        return $this->wallet->transactions()->create([
            'user_id'  => auth()->id(),
            'amount'   => $amount,
            'accepted' => true,
            'meta'     => $meta,
            'type'     => __FUNCTION__,
        ]);
    }

}
