<?php

namespace Mollsoft\PaymentGatewayAPI\DTO;

use Decimal\Decimal;

class BalanceItem
{
    public function __construct(
        public readonly CurrencyItem $currency,
        public readonly Decimal $available,
        public readonly Decimal $pending,
    )
    {}
}