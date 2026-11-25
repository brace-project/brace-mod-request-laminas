<?php


namespace Brace\Mod\Request\Zend;


use Brace\Core\BraceResponseFactoryInterface;
use Brace\Core\NdjsonStream;
use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Diactoros\Response\TextResponse;
use Laminas\Diactoros\ResponseFactory;
use Psr\Http\Message\ResponseInterface;

class LaminasResponseFactoryBridge extends ResponseFactory implements BraceResponseFactoryInterface
{

    public function createResponseWithBody(string $body, int $code = 200, array $headers=[]): ResponseInterface
    {
        return new TextResponse($body, $code, $headers);
    }

    public function createResponseWithoutBody(int $code = 200, array $headers=[]): ResponseInterface
    {
        return new EmptyResponse($code, $headers);
    }
    
    public function createStreamedResponse(\Closure $callback, int $code = 200, array $headers=[]): ResponseInterface
    {
        return new StreamedResponse($callback, $code, $headers);
    }
    
    /** @inheritDoc */
    public function createNdjsonResponse(\Closure $callback, int $code = 200, array $headers = []): ResponseInterface
    {
        return $this->createStreamedResponse(
            static function () use ($callback): void {
                $callback(new NdjsonStream());
            },
            $code,
            $headers
        )
            ->withHeader('Content-Type', 'application/x-ndjson; charset=utf-8')
            ->withHeader('Cache-Control', 'no-store, no-transform')
            ->withHeader('X-Accel-Buffering', 'no')
            ->withoutHeader('Content-Length')
            ->withoutHeader('Transfer-Encoding')
            ->withoutHeader('Content-Encoding');
    }
}
