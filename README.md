# Laravel Cashier Wallet Package

[![License](https://img.shields.io/github/license/alireza2000sajedi/laravel-cashier)](LICENSE)

A powerful package for Laravel that facilitates seamless management of virtual currency, transaction handling, balance management, and fund transfers within your applications.

## Features

- **Wallet Management:** Create and manage wallets associated with users or other entities.
- **Payment Handling:** Manage payments, including initiation, processing, and transaction logging.
- **Transaction Management:** Record and track all wallet-related transactions, including deposits and withdrawals.
- **Configurable Withdraw Ceiling:** Set a ceiling limit for withdrawals through configuration.

## Requirements:

- Laravel Version: 10 or higher
- PHP Version: 8.0 or higher

## Installation

To install the package, use Composer:

```bash
composer require ars/laravel-cashier
```

After installation, publish the configuration and migration files:

```bash
php artisan vendor:publish --tag="cashier-config"
php artisan vendor:publish --tag="cashier-migrations"
```

Then, run the migrations:

```bash
php artisan migrate
```

## Configuration

The package comes with a configuration file located at `config/cashier.php`. Key configuration options include:

- **`wallet.ceiling_withdraw`:** The default withdrawal ceiling.
- **`tables`:** Customizable table names for wallets, payments, and transactions.

```php
return [
    'wallet' => [
        'ceiling_withdraw' => 0,
    ],
    'tables' => [
        'wallet' => 'wallets',
        'payment' => 'payments',
        'transaction' => 'transactions',
    ],
];
```

## Usage

### Traits

#### HasWallet

Add the `HasWallet` trait to any model that should have a wallet:

```php
use Ars\Cashier\Models\Traits\HasWallet;

class User extends Model
{
    use HasWallet;
}
```

- **Get Balance:** Retrieve the wallet balance with:

  ```php
  $balance = $user->balance;
  ```

- **Deposit:**

  ```php
  $user->deposit(100, ['description' => 'Initial deposit']);
  ```

  This will increase the user’s wallet balance by 100 and log the transaction.

- **Withdraw:**

  ```php
  $user->withdraw(50, ['description' => 'Purchase withdrawal']);
  ```

  This will decrease the user’s wallet balance by 50, if within the allowable ceiling, and log the transaction.

- **Check Withdraw Capability:**

  ```php
  if ($user->canWithdraw(50, 10)) {
      echo "Withdrawal allowed";
  } else {
      echo "Withdrawal exceeds allowed limits";
  }
  ```

#### HasPay

Add the `HasPay` trait to any model that should handle payments:

```php
use Ars\Cashier\Models\Traits\HasPay;

class Order extends Model
{
    use HasPay;
}
```

- **Initiate Payment:**

  ```php
  $payment = $order->requestPay('AUTH123', 200, ['order_id' => $order->id]);
  ```

  This starts a payment process and logs it as a transaction.

- **Process Payment Result:**

  ```php
  $result = $order->resultPay('AUTH123', '00', 'REF456');
  ```

  This updates the payment details and marks the associated transaction as accepted.

- **Access Payment Transaction:**

  ```php
  $transaction = $payment->transaction;
  ```

  You can access the transaction associated with a specific payment.

### Wallet Model

The `Wallet` model manages user balances and transactions.

- **Get All Transactions:**

  ```php
  $transactions = $wallet->transactions;
  foreach ($transactions as $transaction) {
      echo $transaction->amount . " - " . $transaction->type;
  }
  ```

- **Set Custom Withdrawal Ceiling:**

  ```php
  $wallet->ceilingWithdraw = 500;
  ```

  You can dynamically adjust the ceiling for withdrawals on a specific wallet.

### Payment Model

The `Payment` model handles payment processing and recording.

- **Retrieve Payment by Authority:**

  ```php
  $payment = Payment::where('authority', 'AUTH123')->first();
  ```

  This retrieves a payment record based on its authority code.

- **Update Payment Status:**

  ```php
  $payment->update(['status_code' => '00', 'payed_at' => now()]);
  ```

  This updates the status of a payment, which can be useful in payment processing callbacks.

## License

This package is open-sourced software licensed under the [MIT license](LICENSE).

## Contributing

Contributions are welcome! Please submit a pull request or create an issue for any improvements or suggestions.

## Support

For any issues, please visit the [Issues page](https://github.com/alireza2000sajedi/laravel-cashier/issues).
