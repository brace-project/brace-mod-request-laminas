<?php

use Brace\Core\BraceApp;
use Brace\Core\NdjsonStream;
use Psr\Http\Message\ResponseInterface;

// Register this handler for GET /api/stream in your Brace application.
function streamAction(BraceApp $app): ResponseInterface
{
    return $app->responseFactory->createNdjsonResponse(function (NdjsonStream $stream): void {
        $stream->send(['type' => 'log', 'message' => 'Started']);
        sleep(1); // Replace with a step of your long-running process.
        $stream->send(['type' => 'result', 'success' => true]);
    });
}
