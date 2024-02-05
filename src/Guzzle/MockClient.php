<?php

namespace MilesChou\Mocker\Guzzle;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Promise\PromiseInterface;
use MilesChou\Psr\Http\Message\Testing\TestRequest;
use OutOfBoundsException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class MockClient implements ClientInterface
{
    /**
     * @var bool
     */
    private $booted = false;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var array<int, array<RequestInterface>>
     */
    private $history = [];

    /**
     * @var array<ResponseInterface|Throwable>
     */
    private $queue = [];

    /**
     * @param ResponseInterface|Throwable|array<ResponseInterface|Throwable> $queue
     * @return MockClient
     */
    public static function create($queue = []): self
    {
        return new self($queue);
    }

    /**
     * @param ResponseInterface|Throwable|array<ResponseInterface|Throwable> $queue
     */
    public function __construct($queue = [])
    {
        if (!is_array($queue)) {
            $queue = [$queue];
        }

        $this->appendQueue($queue);
    }

    /**
     * @param ResponseInterface|Throwable $item
     * @return $this
     */
    public function append($item): self
    {
        if ($item instanceof ResponseInterface) {
            return $this->appendResponse($item);
        }

        // Throwable
        return $this->appendThrowable($item);
    }

    /**
     * @param array<ResponseInterface|Throwable> $items
     * @return $this
     */
    public function appendQueue(array $items): self
    {
        foreach ($items as $item) {
            $this->append($item);
        }

        return $this;
    }

    /**
     * @param ResponseInterface $response
     * @return $this
     */
    public function appendResponse(ResponseInterface $response): self
    {
        $this->queue[] = $response;

        return $this;
    }

    /**
     * @param Throwable $exception
     * @return $this
     */
    public function appendThrowable(Throwable $exception): self
    {
        $this->queue[] = $exception;

        return $this;
    }

    /**
     * @param int $status
     * @return $this
     */
    public function appendEmptyResponse(int $status = 200): self
    {
        return $this->appendResponseWith('', $status);
    }

    /**
     * @param string $body
     * @param int $status
     * @param array<mixed> $headers
     * @return $this
     */
    public function appendResponseWith(string $body = '', int $status = 200, array $headers = []): self
    {
        return $this->appendResponse(MockBuilder::createResponse($body, $status, $headers));
    }

    /**
     * @param mixed $data
     * @param int $status
     * @param array<mixed> $headers
     * @return $this
     */
    public function appendResponseWithJson($data = [], int $status = 200, array $headers = []): self
    {
        return $this->appendResponse(MockBuilder::createResponseByJson($data, $status, $headers));
    }

    /**
     * @param bool $force
     * @return Client
     */
    public function build(bool $force = false): Client
    {
        if ($this->booted && !$force) {
            return $this->client;
        }

        $this->booted = true;
        $this->history = [];

        return $this->client = MockBuilder::createClient($this->queue, $this->history);
    }

    /**
     * @inheritDoc
     */
    public function getConfig($option = null)
    {
        $this->build();

        return $this->client->getConfig($option);
    }

    /**
     * @return array
     */
    public function getHistory(): array
    {
        return $this->history;
    }

    /**
     * @inheritDoc
     */
    public function send(RequestInterface $request, array $options = []): ResponseInterface
    {
        $this->build();

        return $this->client->send($request, $options);
    }

    /**
     * @inheritDoc
     */
    public function sendAsync(RequestInterface $request, array $options = []): PromiseInterface
    {
        $this->build();

        return $this->client->sendAsync($request, $options);
    }

    /**
     * @inheritDoc
     */
    public function request($method, $uri, array $options = []): ResponseInterface
    {
        $this->build();

        return $this->client->request($method, $uri, $options);
    }

    /**
     * @inheritDoc
     */
    public function requestAsync($method, $uri, array $options = []): PromiseInterface
    {
        $this->build();

        return $this->client->requestAsync($method, $uri, $options);
    }

    /**
     * @param int $index
     * @return TestRequest
     */
    public function testRequest(int $index = 0): TestRequest
    {
        if (!isset($this->history[$index]['request'])) {
            throw new OutOfBoundsException("Request index '{$index}' is not found");
        }

        return new TestRequest($this->history[$index]['request']);
    }
}
