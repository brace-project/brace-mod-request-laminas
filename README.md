# brace-mod-request-laminas
Bindings for laminas framework (former ZendFramework) HttpServerRequest / HttpServerResponse



## Streaming a Response


```php

public function streamAction() {
    return new StreaedResponse(function() {
        echo "Hello World";
        flush();
        return true;
    }, 200, ['Content-Type' => 'text/plain']);
}
```