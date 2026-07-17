<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\OpenApi\Model\Operation;
use App\Entity\Trait\DateCreatedTimestampTrait;
use App\Entity\Trait\DateUpdatedTimestampTrait;
use App\Repository\BookAuthorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\MaxDepth;

#[ORM\Entity(repositoryClass: BookAuthorRepository::class)]
#[ORM\Table(options: ['comment' => 'Автор книги'])]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new Get(
            requirements: ['id' => '\d+'],
            openapi: new Operation(
                summary: 'Получить автора книги',
            ),
            normalizationContext: [
                'groups' => [
                    self::GROUP_BOOK_AUTHOR_READ,
                    Book::GROUP_BOOK_READ,
                    Product::GROUP_PRODUCT_READ,
                ],
                'enable_max_depth' => true,
            ],
        ),
    ],
    order: ['id' => 'DESC'],
    security: "is_granted('ROLE_USER')"
)]
class BookAuthor
{
    use DateCreatedTimestampTrait;
    use DateUpdatedTimestampTrait;

    public const string GROUP_BOOK_AUTHOR_READ = 'book_author:read';
    public const string GROUP_BOOK_AUTHOR_WRITE = 'book_author:write';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([Book::GROUP_BOOK_READ, Product::GROUP_PRODUCT_READ])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups([Book::GROUP_BOOK_READ, Book::GROUP_BOOK_WRITE, Product::GROUP_PRODUCT_READ])]
    private ?string $firstName = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups([Book::GROUP_BOOK_READ, Book::GROUP_BOOK_WRITE, Product::GROUP_PRODUCT_READ])]
    private ?string $lastName = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Groups([Book::GROUP_BOOK_READ, Product::GROUP_PRODUCT_READ])]
    private ?\DateTimeImmutable $dateUpdated = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Groups([Book::GROUP_BOOK_READ, Product::GROUP_PRODUCT_READ])]
    private ?\DateTimeImmutable $dateCreated = null;

    /**
     * @var Collection<int, Book>
     */
    #[ORM\OneToMany(targetEntity: Book::class, mappedBy: 'bookAuthor')]
    #[Groups([self::GROUP_BOOK_AUTHOR_READ])]
    #[MaxDepth(1)]
    private Collection $books;

    public function __construct()
    {
        $this->books = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFullName(): string
    {
        return trim($this->getFirstName() . ' ' . $this->getLastName());
    }

    /**
     * @return Collection<int, Book>
     */
    public function getBooks(): Collection
    {
        return $this->books;
    }

    public function addBook(Book $book): static
    {
        if (!$this->books->contains($book)) {
            $this->books->add($book);
            $book->setBookAuthor($this);
        }

        return $this;
    }

    public function removeBook(Book $book): static
    {
        if ($this->books->removeElement($book)) {
            if ($book->getBookAuthor() === $this) {
                $book->setBookAuthor(null);
            }
        }

        return $this;
    }
}
