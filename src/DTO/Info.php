<?php

namespace Mollsoft\PaymentGatewayAPI\DTO;

use Decimal\Decimal;

/**
 * @property CurrencyItem[] $currencies
 */
class Info
{
    public function __construct(
        public readonly array $currencies
    )
    {
    }

    public static function fromArray(array $data): self
    {
        $currencies = [];

        foreach( $data['currencies'] as $item ) {
            $currency = CurrencyItem::fromArray($item);;
            $currencies[$item['name']] = $currency;
        }

        return new self($currencies);
    }
}