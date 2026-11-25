<?php

namespace spec\Brace\Mod\Request\Zend;

use Brace\Core\BraceResponseFactoryInterface;
use Brace\Core\NdjsonStream;
use Brace\Mod\Request\Zend\StreamedResponse;
use PhpSpec\ObjectBehavior;
use RuntimeException;

class LaminasResponseFactoryBridgeSpec extends ObjectBehavior
{
    public function it_creates_a_psr_response_with_body(): void
    {
        $this->shouldImplement(BraceResponseFactoryInterface::class);
        $response = $this->createResponseWithBody('smoke', 201, ['X-Test' => 'yes']);

        $response->getStatusCode()->shouldBe(201);
        $response->getHeaderLine('X-Test')->shouldBe('yes');
        $response->getBody()->__toString()->shouldBe('smoke');
    }

    public function it_creates_an_empty_response(): void
    {
        $response = $this->createResponseWithoutBody(204);

        $response->getStatusCode()->shouldBe(204);
        $response->getBody()->__toString()->shouldBe('');
    }

    public function it_defers_the_ndjson_callback_until_emission(): void
    {
        $called = false;
        $response = $this->createNdjsonResponse(static function (NdjsonStream $stream) use (&$called): void {
            $called = true;
        });

        $response->shouldBeAnInstanceOf(StreamedResponse::class);
        $response->getHeaderLine('Content-Type')->shouldBe('application/x-ndjson; charset=utf-8');
        $response->getHeaderLine('X-Accel-Buffering')->shouldBe('no');
        $response->hasHeader('Content-Length')->shouldBe(false);

        if ($called) {
            throw new RuntimeException('Streaming callback ran too early.');
        }

        $response->__emit();

        if (!$called) {
            throw new RuntimeException('Streaming callback was not executed.');
        }
    }
}
