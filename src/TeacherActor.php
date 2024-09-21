<?php

declare(strict_types=1);

namespace Example;

use Example\Message\FinishTest;
use Example\Message\PrepareTest;
use Example\Message\StartTest;
use Example\Message\SubmitTest;
use Phluxor\ActorSystem\Context\ContextInterface;
use Phluxor\ActorSystem\Message\ActorInterface;
use Phluxor\ActorSystem\Props;
use Phluxor\ActorSystem\Ref;

class TeacherActor implements ActorInterface
{
    /** @var SubmitTest[] */
    private array $endOfTests = [];

    /**
     * @param string $subject
     * @param array $students
     * @param Ref $replyTo
     */
    public function __construct(
        private readonly string $subject,
        private readonly array $students,
        private readonly Ref $replyTo
    ) {
    }

    public function receive(ContextInterface $context): void
    {
        $msg = $context->message();
        switch (true) {
            case $msg instanceof PrepareTest:
                $context->logger()->info(
                    sprintf("Teacher has issued a %s test", $msg->subject)
                );
                foreach ($this->students as $student) {
                    $ref = $context->spawnNamed(
                        Props::fromProducer(fn() => new StudentActor()),
                        sprintf('student-%d', $student)
                    );
                    $context->send($ref->getRef(), new StartTest($msg->subject));
                }
                break;
            case $msg instanceof SubmitTest:
                $this->endOfTests[] = $msg;
                if (count($this->endOfTests) === count($this->students)) {
                    $context->send($this->replyTo, new FinishTest($this->subject));
                    $context->poison($context->self());
                }
                break;
        }
    }
}
