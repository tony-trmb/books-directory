<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Extractor\Dto\ImageExtractorDto;
use App\Extractor\ImageExtractor;
use App\Repository\BookRepository;
use App\State\CreateBookProcessor;
use App\State\UpdateBookProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\InverseJoinColumn;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinTable;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: BookRepository::class)]
#[ApiResource(
    operations: [
        new Get(normalizationContext: ['groups' => ['book:read']]),
        new GetCollection(normalizationContext: ['groups' => ['book:read']]),
        new Post(
            uriTemplate: '/books',
            normalizationContext: ['groups' => 'book:post'],
            denormalizationContext: ['groups' => 'book:write'],
            processor: CreateBookProcessor::class
        ),
        new Patch(
            uriTemplate: '/books',
            normalizationContext: ['groups' => 'book:patch'],
            denormalizationContext: ['groups' => 'book:patch'],
            processor: UpdateBookProcessor::class
        )
    ]
)]
#[ApiFilter(SearchFilter::class, properties: ['id' => 'exact', 'name' => 'exact', 'authors.lastname' => 'exact'])]
final class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['book:read', 'book:post', 'book:write', 'author:read', 'book:patch'])]
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[Groups(['book:read', 'book:post', 'book:write', 'author:read', 'book:patch'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[Groups(['book:read', 'book:post', 'book:write', 'book:patch'])]
    #[ORM\Column(length: 2048, nullable: true)]
    private ?string $image = null;

    #[Groups(['book:read', 'book:post', 'book:write', 'book:patch'])]
    #[ORM\Column(length: 255)]
    private ?string $publishDate = null;

    /**
     * @var Collection<array-key, Author>
     */
    #[ORM\ManyToMany(targetEntity: Author::class, inversedBy: 'books', cascade: ['persist'])]
    #[JoinTable(name: 'book_authors')]
    #[Groups(['book:read', 'book:post', 'book:write', 'book:patch'])]
    #[JoinColumn(name: 'book_id', referencedColumnName: 'id')]
    #[InverseJoinColumn(name: 'author_id', referencedColumnName: 'id')]
    private Collection $authors;

    public function __construct()
    {
        $this->authors = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getImage(): ?string
    {
        if (null === $this->image) {
            return null;
        }

        return (new ImageExtractor())->extract(
            ImageExtractorDto::create($this->image, $this->name)
        );
    }

    public function getEncodedImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getPublishDate(): ?string
    {
        return $this->publishDate;
    }

    public function setPublishDate(string $publishDate): static
    {
        $this->publishDate = $publishDate;

        return $this;
    }

    public function addAuthor(Author $author): static
    {
        if (!$this->authors->contains($author)) {
            $this->authors->add($author);
        }

        return $this;
    }

    public function removeAuthor(Author $author): static
    {
        $this->authors->removeElement($author);

        return $this;
    }

    public function clearAuthors(): static
    {
        $this->authors->clear();

        return $this;
    }

    public function getAuthors(): array
    {
        return $this->authors->toArray();
    }
}
