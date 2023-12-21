# Payment Gateway API

PHP SDK module for working with the Payment Gateway API

## Installation and connection

Installation using [composer](https://getcomposer.org/download/):

```bash
composer require mollsoft/payment-gateway-api
```

## Examples

```php
$baseURI = 'https://..../api';
$merchantId = '...';
$apiKey = '...';

$api = new \Mollsoft\PaymentGatewayAPI\PaymentGatewayAPI($baseURI, $merchantId, $apiKey);

print_r($api->info());
print_r($api->balances());
print_r($api->deposits()->get());
print_r($api->depositAddress('BTC', 'test'));
```