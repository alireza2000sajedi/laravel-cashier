<?php

namespace Ars\Cashier\Models\Interfaces;

interface HasPayInterface
{
    public function callPayment(): mixed;

    public function callbackPayment(): mixed;

    public function getPaymentName(): string;
}