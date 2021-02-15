<?php


namespace Brace\Mod\Request\Zend;


use Brace\Core\EmitterInferface;
use Laminas\HttpHandlerRunner\Emitter\EmitterStack;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Laminas\HttpHandlerRunner\Emitter\SapiStreamEmitter;
use Psr\Http\Message\ResponseInterface;

class LaminasEmitterBridge implements EmitterInferface
{

    public function emit(ResponseInterface $response) : void
    {
        $stack = new EmitterStack();
        $stack->push(new SapiEmitter());
        $stack->push(new SapiStreamEmitter());
        $stack->emit($response);
    }
}