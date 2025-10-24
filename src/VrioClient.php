<?php

namespace Sudipta\Vrio;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;

class VrioClient
{
    protected ClientInterface $http;

    public function __construct(ClientInterface $http)
    {
        $this->http = $http;
    }

    protected function request(string $method, string $uri, array $options = [])
    {
        try {
            $response = $this->http->request($method, ltrim($uri, '/'), $options);
            $body = (string) $response->getBody();

            return [
                'success' => true,
                'code' => $response->getStatusCode(),
                'body' => json_decode($body, true),

            ];
            // return json_decode($body, true);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();
            $statusCode = $response ? $response->getStatusCode() : 0;
            $body = $response ? (string) $response->getBody() : null;
            $json = $body ? json_decode($body, true) : null;

            return [
                'success' => false,
                'code' => $statusCode,
                'error' => $json['error'] ?? null,
                'message' => $json['error']['message'] ?? $e->getMessage(),
            ];
        } catch (\GuzzleHttp\Exception\ServerException $e) {
            return [
                'success' => false,
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ];
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            return [
                'success' => false,
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ];
        }
    }

    // === Customers ===
    public function createCustomer(array $payload)
    {
        return $this->request('POST', 'customers', ['json' => $payload]);
    }

    public function getCustomer(int $customerId)
    {
        return $this->request('GET', "customers/{$customerId}");
    }

    // === Cards ===
    public function addCard(int $customerId, array $payload)
    {
        return $this->request('POST', "customers/{$customerId}/cards", ['json' => $payload]);
    }

    public function listCards(int $customerId)
    {
        return $this->request('GET', "customers/{$customerId}/cards");
    }

    // === Orders ===
    public function createOrder(array $payload)
    {
        return $this->request('POST', 'orders', ['json' => $payload]);
    }

    public function getOrder(int $orderId)
    {
        return $this->request('GET', "orders/{$orderId}");
    }

    // Add more endpoints as needed (subscriptions, refunds, etc).
}
