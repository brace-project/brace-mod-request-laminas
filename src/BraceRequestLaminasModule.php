<?php


namespace Brace\Mod\Request\Zend;


use Brace\Core\BraceApp;
use Brace\Core\BraceModule;
use Laminas\Diactoros\ResponseFactory;
use Laminas\Diactoros\ServerRequestFactory;
use Phore\Di\Container\Producer\DiService;
use Psr\Http\Message\ResponseFactoryInterface;


class BraceRequestLaminasModule implements BraceModule
{

    public function register (BraceApp $braceApp)
    {
        $braceApp->define("request", new DiService(
            function ()  {
                return ServerRequestFactory::fromGlobals();
            }
        ));

        $braceApp->define("response", new DiService(
            function (ResponseFactoryInterface $responseFactory) {
                return $responseFactory->createResponse();
            }
        ));
        $braceApp->define("responseFactory", new DiService(
            function () {
                return new LaminasResponseFactoryBridge();
            }
        ));
        $braceApp->define("emitter", new DiService(
            function () {
                return new LaminasEmitterBridge();
            }

        ));
    }

}