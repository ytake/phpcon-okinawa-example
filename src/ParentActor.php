<?php

declare(strict_types=1);

namespace Example;

use Example\Message\Hello;
use Phluxor\ActorSystem\Context\ContextInterface;
use Phluxor\ActorSystem\Message\ActorInterface;
use Phluxor\ActorSystem\Props;
use Phluxor\ActorSystem\ProtoBuf\Terminated;

class ParentActor implements ActorInterface
{
    public function receive(ContextInterface $context): void
    {
        $message = $context->message();
        switch (true) {
            case $message instanceof Hello:
                $ref = $context->spawn(Props::fromProducer(fn() => new ChildActor()));
                $context->send($ref, $message);
                break;
            case $message instanceof Terminated:
                $context->logger()->info('terminated', [
                    'who' => $message->getWho()->getId(),
                    'why' => $message->getWhy(),
                ]);
                break;
        }
    }
}
