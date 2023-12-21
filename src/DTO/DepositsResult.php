<?php

namespace Mollsoft\PaymentGatewayAPI\DTO;

/**
 * @property DepositItem[] $items
 */
class DepositsResult
{
    public function __construct(
        public readonly array $items,
    )
    {}

    public static function fromArray(array $data): self
    {
        $items = [];
        foreach( $data['data'] as $item ) {
            $items[] = DepositItem::fromArray($item);
        }

        return new self($items);
    }
}