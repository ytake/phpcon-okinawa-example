<?php

declare(strict_types=1);

namespace Example;

use Example\Message\Hello;
use Phluxor\ActorSystem\Context\ContextInterface;
use Phluxor\ActorSystem\Message\ActorInterface;
use Phluxor\ActorSystem\Message\Restarting;

class ChildActor implements ActorInterface
{
    /**
     * @throws \Exception
     */
    public function receive(ContextInterface $context): void
    {
        $message = $context->message();
        switch (true) {
            case $message instanceof Restarting:
                $context->logger()->info('restarting...');
                break;
            case $message instanceof Hello:
                $context->logger()->info('Hello ' . $message->name);
                // $context->stop($context->self()); // notifies the parent actor
                throw new \Exception('hi, I am an exception');
                break;
        }
    }
}
