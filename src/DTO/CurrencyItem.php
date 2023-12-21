<?php

namespace Mollsoft\PaymentGatewayAPI\DTO;

class CurrencyItem
{
    public function __construct(
        public readonly string $name,
        public readonly string $title,
        public readonly string $symbol,
        public readonly int $decimals
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self($data['name'], $data['title'], $data['symbol'], $data['decimals']);
    }
}