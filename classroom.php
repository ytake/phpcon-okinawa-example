<?php

declare(strict_types=1);

use Example\ClassroomActor;
use Example\Message\ClassFinished;
use Example\Message\StartsClass;
use Phluxor\ActorSystem;
use Phluxor\ActorSystem\Context\ContextInterface;
use Phluxor\ActorSystem\Message\ReceiveFunction;
use Phluxor\ActorSystem\Props;

use function Swoole\Coroutine\run;

require_once __DIR__ . '/vendor/autoload.php';

run(function () {
    \Swoole\Coroutine\go(function () {
        $subject = 'math';
        $system = ActorSystem::create();
        $pipe = $system->root()->spawn(
            Props::fromFunction(
                new ReceiveFunction(
                    function (ContextInterface $context): void {
                        $msg = $context->message();
                        if ($msg instanceof ClassFinished) {
                            $context->logger()->info(
                                sprintf('The class has ended: %s', $msg->subject)
                            );
                        }
                    }
                )
            )
        );
        $stream = $system->root()->spawnNamed(
            Props::fromProducer(
                fn() => new ClassroomActor($pipe, $subject, range(1, 20))
            ),
            'math-classroom'
        );
        $system->root()->send($stream->getRef(), new StartsClass($subject));
    });
});
