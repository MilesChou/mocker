# Usage for Guzzle

## `MockBuilder`

`MockBuilder` is a helper to build Guzzle Client with MockHandler.

```php
use GuzzleHttp\Psr7\Response;
use MilesChou\Mocker\Guzzle\MockBuilder;

$expected = new Response();
$history = [];

$client = MockBuilder::createClient($expected, $history);
$client->get('somewhere'); // will return $expected

$history[0]['request']; // The Guzzle request
```

Sure, `MockBuilder` supports PSR-18 client, too.

```php
use MilesChou\Mocker\Guzzle\MockBuilder;

$expected = new Psr7Response();
$history = [];

$client = MockBuilder::createPsr18Client($expected, $history);
$client->sendRequest(new Psr7Request());

$history[0]['request']; // The Guzzle request
```

### `MockClient`

MockClient implements Guzzle `ClientInterface`. Create MockClient and setup behavior.

```php
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use MilesChou\Mocker\Guzzle\MockClient;

$expected = new Response();

$mock = MockClient::create($expected);

$mock->send(new Request('GET', '/foo')); // Will return $expected
```

MockClient supports many helper for setup responses.

```php
use GuzzleHttp\Psr7\Request;
use MilesChou\Mocker\Guzzle\MockClient;

$mock = MockClient::create();
$mock->appendEmptyResponse();
$mock->appendResponseWith('string body');
$mock->appendResponseWithJson(['foo' => 'bar']);

$mock->send(new Request('GET', '/foo')); // Will return empty response
$mock->send(new Request('GET', '/foo')); // Will return 'string body'
$mock->send(new Request('GET', '/foo')); // Will return JSON body
```

MockClient can append Exception, too.

```php
use GuzzleHttp\Psr7\Request;
use MilesChou\Mocker\Guzzle\MockClient;

$mock = new MockClient();
$mock->appendThrowable(new \Exception());

$mock->send(new Request('GET', '/foo')); // Will throw exception
```

MockClient supports use case for spy and custom assertion.

```php
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use MilesChou\Mocker\Guzzle\MockClient;

$expected = (new Request('POST', '/foo'));

$mock = new MockClient(new Response());

$mock->send($expected);

$mock->testRequest(0)
    ->assertMethod('POST')
    ->assertUri('/foo');
```

MockClient just implement Guzzle ClientInterface so that cannot use MockClient instead Guzzle Client. However, MockClient can return Guzzle Client with MockHandler use `build()` method.

```php
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use MilesChou\Mocker\Guzzle\MockClient;

$mock = new MockClient(new Response());

$client = $mock->build();

$client = $mock->build(); // Get the same instance
$client->send(new Request('GET', '/foo'));

$client->testRequest()
    ->assertMethod('GET')
    ->assertUri('/foo');

$client = $mock->build(true); // Force rebuild the object
$client->send(new Request('GET', '/foo'));

$client->testRequest()
    ->assertMethod('GET')
    ->assertUri('/bar');
```
