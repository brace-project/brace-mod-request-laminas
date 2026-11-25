<?php

namespace spec\Brace\Mod\Request\Zend;

use Brace\Core\BraceApp;
use Brace\Core\Helper\QueryParams;
use Brace\Mod\Request\Zend\LaminasEmitterBridge;
use Brace\Mod\Request\Zend\LaminasResponseFactoryBridge;
use Laminas\Diactoros\ServerRequest;
use PhpSpec\ObjectBehavior;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

class BraceRequestLaminasModuleSpec extends ObjectBehavior
{
    public function it_resolves_real_request_services(): void
    {
        $server = $_SERVER;
        $query = $_GET;

        try {
            $_SERVER = [
                'REQUEST_METHOD' => 'GET',
                'REQUEST_URI' => '/smoke?check=yes',
                'HTTP_HOST' => 'localhost',
                'SERVER_PORT' => '80',
            ];
            $_GET = ['check' => 'yes'];

            $app = new BraceApp();
            $this->register($app);

            $request = $app->resolve('request');
            $params = $app->resolve('queryParams');
            $response = $app->resolve('response');

            if (!$request instanceof ServerRequest || $request->getMethod() !== 'GET'
                || $request->getUri()->getPath() !== '/smoke') {
                throw new RuntimeException('Laminas request service failed.');
            }

            if (!$params instanceof QueryParams || $params->get('check') !== 'yes') {
                throw new RuntimeException('Query parameter service failed.');
            }

            if (!$response instanceof ResponseInterface || $response->getStatusCode() !== 200
                || !$app->resolve('responseFactory') instanceof LaminasResponseFactoryBridge
                || !$app->resolve('emitter') instanceof LaminasEmitterBridge) {
                throw new RuntimeException('Response factory or emitter service failed.');
            }
        } finally {
            $_SERVER = $server;
            $_GET = $query;
        }
    }
}
