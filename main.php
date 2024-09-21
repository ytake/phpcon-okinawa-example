<?php

declare(strict_types=1);

use Phluxor\ActorSystem;

use function Swoole\Coroutine\run;

require_once __DIR__ . '/vendor/autoload.php';

run(function () {
    \Swoole\Coroutine\go(function () {
        $system = ActorSystem::create();
        $ref = $system->root()->spawn(
            ActorSystem\Props::fromProducer(
                fn() => new Example\ParentActor()
            )
        );
        $system->root()->send(
            $ref,
            new Example\Message\Hello('World')
        );
    });
});
