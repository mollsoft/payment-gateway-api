<?php

namespace Mollsoft\PaymentGatewayAPI\DTO;

use Decimal\Decimal;

class DepositItem
{
    public function __construct(
        public readonly string $id,
        public readonly int $timestamp,
        public readonly string $reference,
        public readonly string $currency,
        public readonly string $address,
        public readonly Decimal $amount,
        public readonly string $txid,
        public readonly int $confirmations,
        public readonly bool $confirmed,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'],
            strtotime($data['time_at']),
            $data['reference'],
            $data['currency'],
            $data['address'],
            new Decimal((string)$data['amount']),
            $data['txid'],
            (int)$data['confirmations'],
            (bool)$data['confirmed'],
        );
    }
}