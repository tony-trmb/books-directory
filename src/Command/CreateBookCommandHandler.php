<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Book;
use App\Enum\ImageFormat;
use App\Validator\ImageValidator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class CreateBookCommandHandler
{
    public function __construct(
        #[Autowire(service: 'repository.book')]
        private readonly ServiceEntityRepositoryInterface $bookRepository,
        #[Autowire(service: 'repository.author')]
        private readonly ServiceEntityRepositoryInterface $authorRepository
    ){
    }

    public function __invoke(CreateBookCommand $command): Book
    {
        $validImage = false;

        try {
            (new ImageValidator())->validate($command->image);
            $validImage = true;
        } catch (\Throwable $e) {
        }

        if ($validImage) {
            $uniqueId = \str_replace(
                ['/', '\\'],
                '',
                \substr(\explode( ',', $command->image)[1], 0, 21)
            );
            $imageName = \sprintf(
                '%s_%s',
                $command->name,
                $uniqueId
            );
        }

        $book = new Book();
        $book->setName($command->name);
        $book->setDescription($command->description);
        $book->setImage($imageName ?? null);
        $book->setPublishDate($command->publishDate);

        foreach ($command->authors as $author) {
            $book->addAuthor($author);
            $existingAuthor = $this->authorRepository->findOneBy(
                [
                    'firstName' => $author->getFirstName(),
                    'lastname' => $author->getLastname(),
                    'middleName' => $author->getMiddleName()
                ]
            );

            if (null === $existingAuthor) {
                $this->authorRepository->save($author);
            }
        }

        $this->bookRepository->save($book);

        if (null !== $book->getImage()) {
            $this->saveImage($command->image, $book->getImage());
        }

        return $book;
    }

    private function saveImage(?string $imageData, string $imageName): void
    {
        $data = \explode( ',', $imageData);

        $decodedData = \base64_decode($data[1]);

        $fileFormat = \str_contains($data[0], ImageFormat::PNG_FORMAT->value)
            ? ImageFormat::PNG_FORMAT->value
            : ImageFormat::JPG_FORMAT->value;

        $ifp = \fopen(\sprintf(__DIR__ . '/../Images/%s.%s', $imageName, $fileFormat), 'wb');
        fwrite($ifp, $decodedData);

        fclose($ifp);
    }
}