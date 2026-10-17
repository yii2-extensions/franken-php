<?php

declare(strict_types=1);

namespace yii2\extensions\frankenphp\tests\support\stub;

use yii\web\{IdentityInterface, Request, Response};
use yii2\extensions\psrbridge\http\Application;

/**
 * Exercises the bridge lifecycle with a deterministic response and records finalization attempts.
 *
 * @extends Application<IdentityInterface>
 */
final class WorkerApplication extends Application
{
    /**
     * @var list<bool> Emission results passed to the bridge finalizer.
     */
    public array $finalizations = [];

    public string $responseContent = '';

    public function finalize(bool $success = true): void
    {
        $this->finalizations[] = $success;

        parent::finalize($success);
    }

    /**
     * @param Request $request Current Yii request.
     */
    public function handleRequest($request): Response
    {
        $response = $this->getResponse();
        $response->format = Response::FORMAT_RAW;
        $response->content = $this->responseContent;

        return $response;
    }
}
