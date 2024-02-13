<?php

declare(strict_types=1);

namespace App\Command;

interface CommandBusInterface
{
    /**
     * @throws \Throwable
     */
    public function dispatch(CommandInterface $command): mixed;
}
