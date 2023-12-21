<?php

namespace Mollsoft\PaymentGatewayAPI\DTO;

class DepositAddress
{
    public function __construct(
        public readonly string $address,
        public readonly string $reference,
        public readonly string $currency,
    )
    {}

    public static function fromArray(array $data): self
    {
        return new self(
            $data['address'],
            $data['reference'],
            $data['currency']
        );
    }
}