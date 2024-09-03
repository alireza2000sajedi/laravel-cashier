<?php

namespace Ars\Cashier\Models;

use App\Models\User;
use Ars\Cashier\Models\Traits\HasTransaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Class Wallet
 * @package Ars\Cashier\Models
 *
 * @property float|int $balance
 * @property float|int $ceiling_withdraw
 * @property string $uuid
 */
class Wallet extends Model
{

    use HasTransaction;

    /**
     * The default withdrawal ceiling, fetched from the config.
     *
     * @var float|int
     */
    public float|int $ceilingWithdraw;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('cashier.tables.wallet', 'wallets'));
        $this->ceilingWithdraw = config('cashier.wallet.ceiling_withdraw', 0);
    }

    public function walletable(): MorphTo
    {
        return $this->morphTo();
    }
}
