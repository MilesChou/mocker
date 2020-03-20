<?php

namespace MilesChou\Mocker\Guzzle;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use Http\Adapter\Guzzle6\Client as Psr18Client;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;

class MockBuilder
{
    /**
     * @param ResponseInterface|ResponseInterface[] $responses
     * @param array<int> $history
     * @return Client
     */
    public static function createClient($responses = [], &$history = []): Client
    {
        return new Client(self::createOption($responses, $history));
    }

    /**
     * @param ResponseInterface|ResponseInterface[] $responses
     * @param array<int> $history
     * @return HandlerStack
     */
    public static function createHandlerStack($responses = [], &$history = []): HandlerStack
    {
        if (!is_array($responses)) {
            $responses = [$responses];
        }

        $handler = HandlerStack::create(new MockHandler($responses));
        $handler->push(Middleware::history($history));

        return $handler;
    }

    /**
     * @param ResponseInterface|ResponseInterface[] $responses
     * @param array<int> $history
     * @return array<string>
     */
    public static function createOption($responses = [], &$history = []): array
    {
        return [
            'handler' => self::createHandlerStack($responses, $history),
        ];
    }

    /**
     * @param ResponseInterface|ResponseInterface[] $responses
     * @param array<int> $history
     * @return ClientInterface
     */
    public static function createPsr18Client($responses = [], &$history = []): ClientInterface
    {
        return new Psr18Client(self::createClient($responses, $history));
    }

    /**
     * @param string $body
     * @param int $status
     * @param array<string> $headers
     * @return ResponseInterface
     */
    public static function createResponse(
        string $body = '',
        int $status = 200,
        array $headers = []
    ): ResponseInterface {
        return new Response($status, $headers, $body);
    }

    /**
     * @param array<mixed> $data
     * @param int $status
     * @param array<string> $headers
     * @return ResponseInterface
     */
    public static function createResponseByJson(
        array $data = [],
        int $status = 200,
        array $headers = []
    ): ResponseInterface {
        return self::createResponse((string)json_encode($data), $status, $headers);
    }
}
