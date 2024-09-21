<?php

declare(strict_types=1);

namespace Example;

use Example\Message\ClassFinished;
use Example\Message\FinishTest;
use Example\Message\PrepareTest;
use Example\Message\StartsClass;
use Phluxor\ActorSystem\Context\ContextInterface;
use Phluxor\ActorSystem\Message\ActorInterface;
use Phluxor\ActorSystem\Props;
use Phluxor\ActorSystem\Ref;

readonly class ClassroomActor implements ActorInterface
{
    /**
     * @param Ref $stream
     * @param string $subject
     * @param array $students
     */
    public function __construct(
        private Ref $stream,
        private string $subject,
        private array $students
    ) {
    }

    public function receive(ContextInterface $context): void
    {
        $msg = $context->message();
        switch (true) {
            case $msg instanceof StartsClass:
                $ref = $context->spawn(
                    Props::fromProducer(
                        fn() => new TeacherActor(
                            $this->subject, $this->students, $context->self()
                        )
                    )
                );
                $context->send($ref, new PrepareTest($msg->subject));
                break;
            case $msg instanceof FinishTest:
                $context->send(
                    $this->stream,
                    new ClassFinished($msg->subject)
                );
                \Swoole\Coroutine::sleep(0.1);
                $context->stop($context->self());
                break;
        }
    }
}
