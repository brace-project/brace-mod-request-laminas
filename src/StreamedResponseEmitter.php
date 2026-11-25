<?php

namespace Brace\Mod\Request\Zend;

use Laminas\HttpHandlerRunner\Emitter\EmitterInterface;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitterTrait;
use Laminas\HttpHandlerRunner\Exception\EmitterException;
use Psr\Http\Message\ResponseInterface;

class StreamedResponseEmitter implements EmitterInterface
{
    use SapiEmitterTrait;

    public function emit(ResponseInterface $response): bool
    {
        if (! ($response instanceof StreamedResponse)) {
            return false;
        }

        if (self::isNdjson($response)) {
            $this->prepareNdjsonOutput();
            $response = $response
                ->withoutHeader('Content-Length')
                ->withoutHeader('Transfer-Encoding')
                ->withoutHeader('Content-Encoding');
        }

        if ( ! headers_sent()) {
            $this->emitStatusLine($response);
            $this->emitHeaders($response);
        }
        
        $response->__emit();

        return true;
    }

    /** @internal */
    public static function isNdjson(ResponseInterface $response): bool
    {
        $mediaType = explode(';', $response->getHeaderLine('Content-Type'), 2)[0];

        return $response instanceof StreamedResponse
            && strtolower(trim($mediaType)) === 'application/x-ndjson';
    }

    private function prepareNdjsonOutput(): void
    {
        if (headers_sent()) {
            throw new EmitterException('Cannot start an NDJSON stream after headers were sent.');
        }

        // Verhindert Kompression und erkennt unbeabsichtigte Ausgabe vor dem Stream.
        if (filter_var(ini_get('zlib.output_compression'), FILTER_VALIDATE_BOOLEAN)
            || (int) ini_get('zlib.output_compression') > 0
        ) {
            if (ini_set('zlib.output_compression', '0') === false) {
                throw new EmitterException('Disable zlib.output_compression for NDJSON streaming.');
            }
        }

        // Auch verschachtelte leere Output-Buffer duerfen den Stream nicht puffern.
        while (ob_get_level() > 0) {
            $status = ob_get_status();
            if (ob_get_length() !== 0 || !(($status['flags'] ?? 0) & PHP_OUTPUT_HANDLER_REMOVABLE)) {
                throw new EmitterException('NDJSON requires empty, removable output buffers.');
            }
            if (!ob_end_clean()) {
                throw new EmitterException('Unable to disable output buffering for NDJSON.');
            }
        }
    }
}
