<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Command\CommandBusInterface;
use App\Command\CreateBookCommand;
use App\Entity\Book;
use App\Exception\InvalidRequestBodyException;

class CreateBookProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly CommandBusInterface $commandBus,
    ) {
    }

    /**
     * @throws \Throwable
     * @throws InvalidRequestBodyException
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Book
    {
        if (!$data instanceof Book) {
            throw new InvalidRequestBodyException();
        }

        $command = new CreateBookCommand(
            $data->getName(),
            $data->getDescription(),
            $data->getEncodedImage(),
            $data->getAuthors(),
            $data->getPublishDate()
        );

        return $this->commandBus->dispatch($command);
    }
}
