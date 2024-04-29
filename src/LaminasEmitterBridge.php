<?php


namespace Brace\Mod\Request\Zend;


use Brace\Core\EmitterInferface;
use Laminas\HttpHandlerRunner\Emitter\EmitterStack;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Laminas\HttpHandlerRunner\Emitter\SapiStreamEmitter;
use Laminas\HttpHandlerRunner\Exception\EmitterException;
use Psr\Http\Message\ResponseInterface;

class LaminasEmitterBridge implements EmitterInferface
{

    public function emit(ResponseInterface $response) : void
    {
        $stack = new EmitterStack();

        $stack->push(new SapiEmitter());
        $stack->push(new SapiStreamEmitter());
        $stack->push(new StreamedResponseEmitter());
        @ini_set('zlib.output_compression',0); // Turn off compression (won't work with this)
        @ini_set('implicit_flush',1);
        @ob_end_clean();

        try {
            $stack->emit($response);
        } catch (EmitterException $e) {
            if (headers_sent($file, $line)) {
                throw new \Error($e->getMessage() . " (Output stated in: '$file' on Line: $line)", 0, $e);
            }
            throw new EmitterException($e->getMessage() . " (turn output buffering off to see the file and line - use ob_end_flush() to disable)", $e->getCode(), $e);
        }
    }
}
