<?php

namespace Mollsoft\PaymentGatewayAPI\DTO;

use Mollsoft\PaymentGatewayAPI\PaymentGatewayAPI;

class DepositsBuilder
{
    protected ?string $sort = null;
    protected int $perPage = 20;
    protected int $page = 1;
    protected array $filter = [];

    public function __construct(
        protected readonly PaymentGatewayAPI $api,
    ) {
    }

    public function sort(?string $sort): self
    {
        $this->sort = $sort;

        return $this;
    }

    public function perPage(int $perPage = 20): self
    {
        $this->perPage = $perPage;

        return $this;
    }

    public function page(int $page = 1): self
    {
        $this->page = $page;

        return $this;
    }

    public function where(string $field, string $value): self
    {
        $this->filter[$field] = $value;

        return $this;
    }

    public function whereReference(string $reference): self
    {
        return $this->where('reference', $reference);
    }

    public function whereAddress(string $address): self
    {
        return $this->where('address', $address);
    }

    public function whereTXID(string $txid): self
    {
        return $this->where('txid', $txid);
    }

    public function whereCurrency(string $currency): self
    {
        return $this->where('currency', $currency);
    }

    public function get(): DepositsResult
    {
        $get = [
            'per_page' => $perPage ?? 20,
            'page' => $page ?? 1,
        ];
        if( $this->sort ) {
            $get['sort'] = $this->sort;
        }
        if( $this->filter ) {
            $get['filter'] = $this->filter;
        }

        return DepositsResult::fromArray(
            $this->api->request('/crypto/deposits', $get)
        );
    }
}