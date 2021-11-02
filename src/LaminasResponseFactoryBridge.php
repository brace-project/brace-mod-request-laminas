<?php


namespace Brace\Mod\Request\Zend;


use Brace\Core\BraceResponseFactoryInterface;
use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Diactoros\Response\TextResponse;
use Laminas\Diactoros\ResponseFactory;
use Psr\Http\Message\ResponseInterface;

class LaminasResponseFactoryBridge extends ResponseFactory implements BraceResponseFactoryInterface
{

    public function createResponseWithBody(string $body, int $code = 200, array $headers=[]): ResponseInterface
    {
        return new TextResponse($body, $code, $headers = []);
    }

    public function createResponseWithoutBody(int $code = 200, array $headers=[]): ResponseInterface
    {
        return new EmptyResponse($code, $headers);
    }
}
