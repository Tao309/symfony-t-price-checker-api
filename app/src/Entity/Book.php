<?php

namespace App\Entity;

use App\Entity\Trait\DateCreatedTimestampTrait;
use App\Entity\Trait\DateUpdatedTimestampTrait;
use App\Entity\Trait\UserAwareTrait;
use App\Repository\BookRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookRepository::class)]
#[ORM\Table(options: ['comment' => 'Книги'])]
#[ORM\HasLifecycleCallbacks]
class Book implements UserAwareInterface
{
    use DateUpdatedTimestampTrait, DateCreatedTimestampTrait, UserAwareTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\ManyToOne(inversedBy: 'books')]
    #[ORM\JoinColumn(nullable: false)]
    private ?BookAuthor $BookAuthor = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $originalTitle = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $isbn = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $pages = null;

    #[ORM\Column(nullable: true)]
    private ?int $circulation = null;

    #[ORM\Column(length: 20)]
    private ?string $size = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $publishYear = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?BookBindingType $bindingType = null;

    #[ORM\ManyToOne]
    private ?BookPublishingHouse $publishingHouse = null;

    #[ORM\ManyToOne]
    private ?BookPublishingBrand $publishingBrand = null;

    #[ORM\ManyToOne]
    private ?BookSeries $bookSeries = null;

    #[ORM\Column(nullable: true)]
    private ?int $livelibId = null;

    #[ORM\Column(nullable: true)]
    private ?int $goodreads_id = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $fantlabId = null;

    #[ORM\Column(nullable: true)]
    private ?float $livelibRating = null;

    #[ORM\Column(nullable: true)]
    private ?float $goodreadsRating = null;

    #[ORM\ManyToOne(inversedBy: 'books')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $userCreated = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $dateUpdated = null;

    #[ORM\Column]
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
        return $this->BookAuthor;
    }

    public function setBookAuthor(?BookAuthor $BookAuthor): static
    {
        $this->BookAuthor = $BookAuthor;

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
