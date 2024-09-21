<?php

declare(strict_types=1);

namespace Example\Message;

readonly class SubmitTest
{
    public function __construct(
        public string $subject,
        public string $name
    ) {
    }
}
