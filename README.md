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

See the [NDJSON examples and server configuration in brace/core](https://github.com/brace-project/brace-core/tree/main/examples).
