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
        try {
            (new ImageValidator())->validate($command->image);
            $validImage = true;
        } catch (\Throwable $e) {
            $validImage = false;
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
            $existingAuthor = $this->authorRepository->findOneBy(
                [
                    'firstName' => $author->getFirstName(),
                    'lastname' => $author->getLastname(),
                    'middleName' => $author->getMiddleName()
                ]
            );

            $book->addAuthor(null !== $existingAuthor ? $existingAuthor : $author);
        }

        $this->bookRepository->save($book);

        if (isset($imageName)) {
            $this->saveImage($command->image, $book->getName(), $imageName);
        }

        return $book;
    }

    private function saveImage(?string $imageData, string $bookName, string $imageName): void
    {
        $data = \explode( ',', $imageData);

        $decodedData = \base64_decode($data[1]);

        $fileFormat = \str_contains($data[0], ImageFormat::PNG_FORMAT->value)
            ? ImageFormat::PNG_FORMAT->value
            : ImageFormat::JPG_FORMAT->value;

        $filePath = \sprintf(__DIR__ . '/../Images/%s/', $bookName);

        if (!\is_dir($filePath)) {
            \mkdir($filePath, 0777, true);
        }

        $ifp = \fopen(\sprintf('%s/%s.%s',$filePath, $imageName, $fileFormat), 'wb');
        fwrite($ifp, $decodedData);

        fclose($ifp);
    }
}