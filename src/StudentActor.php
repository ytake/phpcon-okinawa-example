<?php

declare(strict_types=1);

namespace Example;

use Example\Message\StartTest;
use Example\Message\SubmitTest;
use Phluxor\ActorSystem\Context\ContextInterface;
use Phluxor\ActorSystem\Message\ActorInterface;

class StudentActor implements ActorInterface
{
    public function receive(ContextInterface $context): void
    {
        $msg = $context->message();
        if ($msg instanceof StartTest) {
            sleep(random_int(1, 9));
            $context->logger()->info(
                sprintf(
                    '%s is submitting the answer to the %s test',
                    $context->self(),
                    $msg->subject
                )
            );
            $context->send(
                $context->parent(),
                new SubmitTest(
                    $msg->subject,
                    (string)$context->self(),
                )
            );
            $context->poison($context->self());
        }
    }
}
