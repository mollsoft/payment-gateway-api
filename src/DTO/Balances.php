<?php

namespace Mollsoft\PaymentGatewayAPI\DTO;

use Decimal\Decimal;

/**
 * @property CurrencyItem[] $currencies
 */
class Balances
{
    public function __construct(
        public readonly array $currencies,
        public readonly array $balances,
    )
    {
    }

    public static function fromArray(array $data): self
    {
        $currencies = [];
        $balances = [];

        foreach( $data['currencies'] as $item ) {
            $currency = CurrencyItem::fromArray($item);;
            $currencies[$item['name']] = $currency;
            $available = new Decimal((string)($data['balances']['available'][$item['name']] ?? 0));
            $pending = new Decimal((string)($data['balances']['pending'][$item['name']] ?? 0));
            $balances[$item['name']] = new BalanceItem($currency, $available, $pending);
        }

        return new self($currencies, $balances);
    }
}