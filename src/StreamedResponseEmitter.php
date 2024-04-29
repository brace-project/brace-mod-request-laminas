<?php

namespace Brace\Mod\Request\Zend;

use Laminas\HttpHandlerRunner\Emitter\EmitterInterface;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitterTrait;
use Psr\Http\Message\ResponseInterface;

class StreamedResponseEmitter implements EmitterInterface
{
    use SapiEmitterTrait;

    public function emit(ResponseInterface $response): bool
    {
        if (! ($response instanceof StreamedResponse)) {
            return false;
        }
        
        $this->assertNoPreviousOutput();
        $this->emitHeaders($response);
        $this->emitStatusLine($response);
        
        flush();
        $response->__emit();
        
        return true;
    }
}