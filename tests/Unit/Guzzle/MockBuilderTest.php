<?php

namespace Tests\Unit\Guzzle;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use MilesChou\Mocker\Guzzle\MockBuilder;
use OutOfBoundsException;
use Psr\Http\Message\RequestInterface;
use Tests\TestCase;

class MockBuilderTest extends TestCase
{
    /**
     * @test
     */
    public function shouldThrowExceptionWhenNoMockQueue(): void
    {
        $this->expectException(OutOfBoundsException::class);

        MockBuilder::createClient()->get('whatever');
    }

    /**
     * @test
     */
    public function shouldGotPsr18Client(): void
    {
        $expected = new Response();

        $actual = MockBuilder::createPsr18Client($expected)->sendRequest(new Request('GET', '/whatever'));

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function shouldGotPrependResponse(): void
    {
        $expected = new Response();

        $actual = MockBuilder::createClient($expected)->get('whatever');

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function shouldGotResponseWithJsonBody(): void
    {
        $actual = MockBuilder::createResponseByJson(['foo' => 'bar']);

        $this->assertSame('{"foo":"bar"}', (string)$actual->getBody());
    }

    /**
     * @test
     */
    public function shouldSpyRequest(): void
    {
        $history = [];

        MockBuilder::createClient(new Response(), $history)->get('whatever');

        /** @var RequestInterface $actual */
        $actual = $history[0]['request'];

        $this->assertSame('GET', $actual->getMethod());
        $this->assertSame('whatever', (string)$actual->getUri());
    }
}
