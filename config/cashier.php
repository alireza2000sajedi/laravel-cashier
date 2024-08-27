<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cashier Configuration
    |--------------------------------------------------------------------------
    |
    | This option controls various settings for the Cashier package.
    | You can adjust these values based on your application's requirements.
    |
    */

    'wallet' => [
        'ceiling_withdraw' => 0,
    ],

    /*
    |--------------------------------------------------------------------------
    | Table Names
    |--------------------------------------------------------------------------
    |
    | These options allow you to configure the database table names used
    | by the Cashier package's models.
    |
    */

    'tables' => [
        'wallet'      => 'wallets',
        'payment'     => 'payments',
        'transaction' => 'transactions',
    ],
];
