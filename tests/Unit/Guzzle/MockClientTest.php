<?php

namespace Tests\Unit\Guzzle;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use MilesChou\Mocker\Guzzle\MockClient;
use Psr\Http\Message\RequestInterface;
use RuntimeException;
use Tests\TestCase;

class MockClientTest extends TestCase
{
    /**
     * @test
     */
    public function shouldThrowExceptionWhenQueueIsEmpty(): void
    {
        $this->expectException(RuntimeException::class);

        $target = MockClient::create();

        $target->send(new Request('GET', 'whatever'));
    }

    /**
     * @test
     */
    public function shouldGetResponseInQueue(): void
    {
        $expected = new Response();

        $target = MockClient::create($expected);

        $actual = $target->send(new Request('GET', 'whatever'));

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function shouldGetResponseAfterAppendToQueue(): void
    {
        $expected = new Response();

        $target = MockClient::create();
        $target->appendQueue([$expected]);

        $actual = $target->send(new Request('GET', 'whatever'));

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function shouldGetEmptyResponseWhenAppendEmpty(): void
    {
        $target = MockClient::create();
        $target->appendEmptyResponse();

        $actual = $target->send(new Request('GET', 'whatever'));

        $this->assertSame(200, $actual->getStatusCode());
        $this->assertSame('', (string)$actual->getBody());
    }

    /**
     * @test
     */
    public function shouldGetResponseWithString(): void
    {
        $target = MockClient::create();
        $target->appendResponseWith('Hello', 201, ['foo' => 'bar']);

        $actual = $target->send(new Request('whatever', 'GET'));

        $this->assertSame(201, $actual->getStatusCode());
        $this->assertSame(['bar'], $actual->getHeader('foo'));
        $this->assertSame('Hello', (string)$actual->getBody());
    }

    /**
     * @test
     */
    public function shouldGetResponseWithJson(): void
    {
        $target = MockClient::create();
        $target->appendResponseWithJson(['foo' => 'bar']);

        $actual = $target->send(new Request('GET', 'whatever'));

        $this->assertSame('{"foo":"bar"}', (string)$actual->getBody());
    }

    /**
     * @test
     */
    public function shouldGetRequestInSpy(): void
    {
        $target = new MockClient(new Response());

        $target->send(new Request('GET', '/whatever'));

        /** @var RequestInterface $request */
        $request = $target->getHistory()[0]['request'];

        $this->assertSame('GET', $request->getMethod());
        $this->assertSame('/whatever', (string)$request->getUri());
    }

    /**
     * @test
     */
    public function shouldCanTestRequest(): void
    {
        $target = new MockClient(new Response());

        $target->send(new Request('GET', '/whatever'));

        $target->testRequest()
            ->assertMethod('GET')
            ->assertUri('/whatever');
    }

    /**
     * @test
     */
    public function shouldThrowExceptionWhenQueueInThrowable(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Hello');

        $target = new MockClient(new \Exception('Hello'));
        $target->send(new Request('GET', 'whatever'));
    }

    /**
     * @test
     */
    public function shouldReturnSameInstanceOrRecreateWhenBuild(): void
    {
        $target = new MockClient();

        $expected = $target->build();

        $this->assertSame($expected, $target->build());
        $this->assertNotSame($expected, $target->build(true));
    }

    /**
     * @test
     */
    public function shouldSpyRequestWhenUseBuildGuzzleClient(): void
    {
        $target = new MockClient(new Response());

        $target->build()->send(new Request('GET', '/foo'));

        $target->testRequest()
            ->assertMethod('GET')
            ->assertUri('/foo');

        // Force recreate
        $target->build(true)->send(new Request('GET', '/bar'));

        $target->testRequest()
            ->assertMethod('GET')
            ->assertUri('/bar');
    }
}
