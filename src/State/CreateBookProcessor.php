<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\HttpOperation;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Command\CommandBusInterface;
use App\Command\CreateBookCommand;
use App\Entity\Book;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

class CreateBookProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly CommandBusInterface $commandBus,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Book
    {
        /** @var Book $book */
        $book = $data;

        $command = new CreateBookCommand(
            $book->getName(),
            $book->getDescription(),
            $book->getImage(),
            $book->getAuthors(),
            $book->getPublishDate()
        );

        return $this->commandBus->dispatch($command);
    }
}
