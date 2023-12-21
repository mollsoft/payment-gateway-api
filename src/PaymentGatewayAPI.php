<?php

namespace Mollsoft\PaymentGatewayAPI;

use Mollsoft\PaymentGatewayAPI\DTO\Balances;
use Mollsoft\PaymentGatewayAPI\DTO\DepositAddress;
use Mollsoft\PaymentGatewayAPI\DTO\DepositsBuilder;
use Mollsoft\PaymentGatewayAPI\DTO\Info;

class PaymentGatewayAPI
{
    protected readonly ?array $proxy;

    public function __construct(
        protected readonly string $baseURI,
        protected readonly string $merchantId,
        protected readonly string $apiKey,
        ?string $proxy = null,
    ) {
        $this->proxy = $this->parseProxyString($proxy);
    }

    protected function parseProxyString(?string $proxy): ?array
    {
        if ($proxy) {
            $parse = parse_url($proxy);
            if ($parse === false) {
                throw new \Exception('Error parsing proxy string');
            }
            if (!in_array($parse['scheme'] ?? null, ['http', 'https', 'socks4', 'socks5'])) {
                throw new \Exception('Proxy protocol must be http/https/socks4/socks5');
            }
            if (!($parse['host'] ?? null)) {
                throw new \Exception('Proxy host is not defined');
            }
            if (!($parse['port'] ?? null)) {
                throw new \Exception('Proxy port is not defined');
            }

            return [
                'protocol' => $parse['scheme'],
                'host' => $parse['host'],
                'port' => $parse['port'],
                'username' => $parse['user'] ?? null,
                'password' => $parse['pass'] ?? null,
            ];
        }

        return null;
    }

    public function info(): Info
    {
        return Info::fromArray(
            $this->request('/')
        );
    }

    public function balances(): Balances
    {
        return Balances::fromArray(
            $this->request('/merchant')
        );
    }

    public function depositAddress(string $currency, string $reference): DepositAddress
    {
        return DepositAddress::fromArray(
            $this->request('/crypto/deposit-address', null, [
                'currency' => $currency,
                'reference' => $reference,
            ])
        );
    }

    public function deposits(): DepositsBuilder
    {
        return new DepositsBuilder($this);
    }

    public function request(string $path, ?array $get = null, ?array $data = null): array
    {
        if( is_array($get) && count($get) ) {
            $path .= '?'.http_build_query($get);
        }

        $method = $data !== null ? 'POST' : 'GET';
        $timestamp = time();
        $body = $data !== null ? json_encode($data, JSON_UNESCAPED_UNICODE) : null;
        $signature = base64_encode(
            hash_hmac(
                'sha256',
                $timestamp.$method.$path.$body,
                $this->apiKey,
                true
            )
        );

        $headers = [
            'Accept: application/json',
            'Content-Type: application/json;charset=UTF-8',
            'X-Merchant: '.$this->merchantId,
            'X-Timestamp: '.$timestamp,
            'X-Signature: '.$signature,
        ];
        if( $body ) {
            $headers[] = 'Content-Length: '.strlen($body);
        }

        $curl = curl_init($this->baseURI.$path);
        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 45,
            CURLOPT_CONNECTTIMEOUT => 15
        ]);
        if ($body) {
            curl_setopt_array($curl, [
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $body,
            ]);
        }
        if ($this->proxy) {
            curl_setopt_array($curl, [
                CURLOPT_HTTPPROXYTUNNEL => true,
                CURLOPT_PROXY => $this->proxy['host'].':'.$this->proxy['port'],
            ]);
            if ($this->proxy['username']) {
                curl_setopt_array($curl, [
                    CURLOPT_PROXYUSERNAME => $this->proxy['username'],
                    CURLOPT_PROXYPASSWORD => $this->proxy['password'],
                ]);
            }
        }
        $response = curl_exec($curl);
        $errno = curl_errno($curl);
        $error = curl_error($curl);
        curl_close($curl);

        if ($errno) {
            throw new \Exception('Curl '.$errno.' - '.$error);
        }

        $responseArray = @json_decode($response, true);
        if (!$responseArray) {
            throw new \Exception($response);
        }

        if( $responseArray['errors'] ?? null ) {
            throw new \Exception(array_key_first($responseArray['errors']).' - '.$responseArray['message']);
        }

        return $responseArray;
    }
}
