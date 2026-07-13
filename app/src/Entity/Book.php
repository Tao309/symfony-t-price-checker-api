<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Entity\Trait\DateCreatedTimestampTrait;
use App\Entity\Trait\DateUpdatedTimestampTrait;
use App\Entity\Trait\UserAwareTrait;
use App\Repository\BookRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\OpenApi\Model\Operation;

#[ORM\Entity(repositoryClass: BookRepository::class)]
#[ORM\Table(options: ['comment' => 'Книги'])]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new Get(
            requirements: ['id' => '\d+'],
            openapi: new Operation(
                summary: 'Получить книгу',
            ),
            normalizationContext: ['groups' => [self::GROUP_BOOK_READ]]
        ),
        new GetCollection(
            openapi: new Operation(
                summary: 'Получить список книг',
            ),
            normalizationContext: ['groups' => [self::GROUP_BOOK_READ]],
        ),
        new Post(
            openapi: new Operation(
                summary: 'Создать книгу',
            ),
            denormalizationContext: ['groups' => [self::GROUP_BOOK_WRITE]],
        ),
        new Patch(
            requirements: ['id' => '\d+'],
            openapi: new Operation(
                summary: 'Обновить книгу',
            ),
            denormalizationContext: ['groups' => [self::GROUP_BOOK_WRITE]],
        ),
    ],
    order: ['id' => 'DESC'],
    security: "is_granted('ROLE_USER')"
)]
class Book implements UserAwareInterface
{
    use DateUpdatedTimestampTrait, DateCreatedTimestampTrait, UserAwareTrait;

    public const string GROUP_BOOK_READ = 'book:read';
    public const string GROUP_BOOK_WRITE = 'book:write';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([self::GROUP_BOOK_READ])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups([self::GROUP_BOOK_READ, self::GROUP_BOOK_WRITE])]
    #[Assert\NotNull(groups: [self::GROUP_BOOK_WRITE])]
    #[Assert\Length(min: 5, max: 255, groups: [self::GROUP_BOOK_WRITE])]
    private ?string $title = null;

    #[ORM\ManyToOne(inversedBy: 'books')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([self::GROUP_BOOK_READ, self::GROUP_BOOK_WRITE])]
    #[Assert\NotNull(groups: [self::GROUP_BOOK_WRITE])]
    private ?BookAuthor $bookAuthor = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups([self::GROUP_BOOK_READ, self::GROUP_BOOK_WRITE])]
    private ?string $originalTitle = null;

    #[ORM\Column(length: 30, nullable: true)]
    #[Groups([self::GROUP_BOOK_READ, self::GROUP_BOOK_WRITE])]
    #[Assert\Length(max: 20, groups: [self::GROUP_BOOK_WRITE])]
    private ?string $isbn = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    #[Groups([self::GROUP_BOOK_READ, self::GROUP_BOOK_WRITE])]
    private ?int $pages = null;

    #[ORM\Column(nullable: true)]
    #[Groups([self::GROUP_BOOK_READ, self::GROUP_BOOK_WRITE])]
    private ?int $circulation = null;

    #[ORM\Column(length: 20)]
    #[Groups([self::GROUP_BOOK_READ, self::GROUP_BOOK_WRITE])]
    #[Assert\Positive(groups: [self::GROUP_BOOK_WRITE])]
    private ?string $size = null;

    #[ORM\Column(type: Types::SMALLINT)]
    #[Groups([self::GROUP_BOOK_READ, self::GROUP_BOOK_WRITE])]
    #[Assert\Positive(groups: [self::GROUP_BOOK_WRITE])]
    private ?int $publishYear = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([self::GROUP_BOOK_READ, self::GROUP_BOOK_WRITE])]
    #[Assert\NotNull(groups: [self::GROUP_BOOK_WRITE])]
    private ?BookBindingType $bindingType = null;

    #[ORM\ManyToOne]
    #[Groups([self::GROUP_BOOK_READ, self::GROUP_BOOK_WRITE])]
    private ?BookPublishingHouse $publishingHouse = null;

    #[ORM\ManyToOne]
    #[Groups([self::GROUP_BOOK_READ, self::GROUP_BOOK_WRITE])]
    private ?BookPublishingBrand $publishingBrand = null;

    #[ORM\ManyToOne]
    #[Groups([self::GROUP_BOOK_READ, self::GROUP_BOOK_WRITE])]
    private ?BookSeries $bookSeries = null;

    #[ORM\Column(nullable: true)]
    #[Groups([self::GROUP_BOOK_READ, self::GROUP_BOOK_WRITE])]
    private ?int $livelibId = null;

    #[ORM\Column(nullable: true)]
    #[Groups([self::GROUP_BOOK_READ, self::GROUP_BOOK_WRITE])]
    private ?int $goodreads_id = null;

    #[ORM\Column(length: 100, nullable: true)]
    #[Groups([self::GROUP_BOOK_READ, self::GROUP_BOOK_WRITE])]
    private ?string $fantlabId = null;

    #[ORM\Column(nullable: true)]
    #[Groups([self::GROUP_BOOK_READ, self::GROUP_BOOK_WRITE])]
    private ?float $livelibRating = null;

    #[ORM\Column(nullable: true)]
    #[Groups([self::GROUP_BOOK_READ, self::GROUP_BOOK_WRITE])]
    private ?float $goodreadsRating = null;

    #[ORM\JoinColumn(nullable: false)]
    #[Groups([self::GROUP_BOOK_READ])]
    private ?User $userCreated = null;

    #[ORM\Column]
    #[Groups([self::GROUP_BOOK_READ])]
    private ?\DateTimeImmutable $dateUpdated = null;

    #[ORM\Column]
    #[Groups([self::GROUP_BOOK_READ])]
    private ?\DateTimeImmutable $dateCreated = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getBookAuthor(): ?BookAuthor
    {
        return $this->bookAuthor;
    }

    public function setBookAuthor(?BookAuthor $bookAuthor): static
    {
        $this->bookAuthor = $bookAuthor;

        return $this;
    }

    public function getOriginalTitle(): ?string
    {
        return $this->originalTitle;
    }

    public function setOriginalTitle(?string $originalTitle): static
    {
        $this->originalTitle = $originalTitle;

        return $this;
    }

    public function getIsbn(): ?string
    {
        return $this->isbn;
    }

    public function setIsbn(?string $isbn): static
    {
        $this->isbn = $isbn;

        return $this;
    }

    public function getPages(): ?int
    {
        return $this->pages;
    }

    public function setPages(?int $pages): static
    {
        $this->pages = $pages;

        return $this;
    }

    public function getCirculation(): ?int
    {
        return $this->circulation;
    }

    public function setCirculation(?int $circulation): static
    {
        $this->circulation = $circulation;

        return $this;
    }

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function setSize(string $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function getPublishYear(): ?int
    {
        return $this->publishYear;
    }

    public function setPublishYear(int $publishYear): static
    {
        $this->publishYear = $publishYear;

        return $this;
    }

    public function getBindingType(): ?BookBindingType
    {
        return $this->bindingType;
    }

    public function setBindingType(?BookBindingType $bindingType): static
    {
        $this->bindingType = $bindingType;

        return $this;
    }

    public function getPublishingHouse(): ?BookPublishingHouse
    {
        return $this->publishingHouse;
    }

    public function setPublishingHouse(?BookPublishingHouse $publishingHouse): static
    {
        $this->publishingHouse = $publishingHouse;

        return $this;
    }

    public function getPublishingBrand(): ?BookPublishingBrand
    {
        return $this->publishingBrand;
    }

    public function setPublishingBrand(?BookPublishingBrand $publishingBrand): static
    {
        $this->publishingBrand = $publishingBrand;

        return $this;
    }

    public function getBookSeries(): ?BookSeries
    {
        return $this->bookSeries;
    }

    public function setBookSeries(?BookSeries $bookSeries): static
    {
        $this->bookSeries = $bookSeries;

        return $this;
    }

    public function getLivelibId(): ?int
    {
        return $this->livelibId;
    }

    public function setLivelibId(?int $livelibId): static
    {
        $this->livelibId = $livelibId;

        return $this;
    }

    public function getGoodreadsId(): ?int
    {
        return $this->goodreads_id;
    }

    public function setGoodreadsId(?int $goodreads_id): static
    {
        $this->goodreads_id = $goodreads_id;

        return $this;
    }

    public function getFantlabId(): ?string
    {
        return $this->fantlabId;
    }

    public function setFantlabId(?string $fantlabId): static
    {
        $this->fantlabId = $fantlabId;

        return $this;
    }

    public function getLivelibRating(): ?float
    {
        return $this->livelibRating;
    }

    public function setLivelibRating(?float $livelibRating): static
    {
        $this->livelibRating = $livelibRating;

        return $this;
    }

    public function getGoodreadsRating(): ?float
    {
        return $this->goodreadsRating;
    }

    public function setGoodreadsRating(?float $goodreadsRating): static
    {
        $this->goodreadsRating = $goodreadsRating;

        return $this;
    }
}
