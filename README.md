# brace-mod-request-laminas
Bindings for laminas framework (former ZendFramework) HttpServerRequest / HttpServerResponse



## Streaming a Response


```php

public function streamAction() {
    return new StreamedResponse(function() {
        echo "Hello World";
        flush();
        return true;
    }, 200, ['Content-Type' => 'text/plain']);
}
```

## NDJSON streaming

Return `$app->responseFactory->createNdjsonResponse($callback)` and call
`$stream->send($data)` inside the callback.

- [PHP controller](examples/ndjson-handler.php)
- [JavaScript client](examples/ndjson-client.js)
- [Server configuration](examples/NDjson%20Config.md)
