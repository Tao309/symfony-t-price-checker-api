<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Link;
use App\Entity\Trait\DateCreatedTimestampTrait;
use App\Entity\Trait\DateUpdatedTimestampTrait;
use App\Entity\Trait\UserAwareTrait;
use App\Repository\BookUserDataRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Attribute\Context;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

#[ORM\Entity(repositoryClass: BookUserDataRepository::class)]
#[ORM\UniqueConstraint(name: 'bud_book_user', columns: ['book_id', 'user_created_id'])]
#[ORM\Table(options: ['comment' => 'Пользовательские данные по книгам'])]
#[UniqueEntity(
    fields: ['book_id', 'user_created_id'],
    message: 'BookUserData с такой комбинацией полей уже существует'
)]
#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/book_user_data/{book}/{userCreated}',
            uriVariables: [
                'book' => new Link(fromClass: BookUserData::class, identifiers: ['book.id']),
                'userCreated' => new Link(fromClass: BookUserData::class, identifiers: ['userCreated.id']),
            ],
        ),
    ],
    security: "is_granted('ROLE_USER')"
)]
#[ORM\HasLifecycleCallbacks]
class BookUserData
{
    use DateCreatedTimestampTrait;
    use DateUpdatedTimestampTrait;
    use UserAwareTrait;

    #[ORM\Id]
    #[ApiProperty(identifier: true)]
    #[ORM\OneToOne(targetEntity: Book::class, inversedBy: 'bookUserData')]
    private ?Book $book;

    #[ORM\Id]
    #[ApiProperty(identifier: true)]
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $userCreated;

    #[ORM\Column(type: Types::DATETIMETZ_MUTABLE, nullable: true)]
    #[Groups([Product::GROUP_READ, Book::GROUP_BOOK_READ])]
    private ?\DateTime $releaseDate = null;

    #[ORM\Column(nullable: true)]
    #[Groups([Product::GROUP_READ, Book::GROUP_BOOK_READ])]
    private ?int $listenPriceValue = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups([Product::GROUP_READ, Book::GROUP_BOOK_READ])]
    private ?string $comment = null;

    #[ORM\Column(type: Types::DATETIMETZ_MUTABLE)]
    #[Groups([Product::GROUP_READ, Book::GROUP_BOOK_READ])]
    #[Context([DateTimeNormalizer::FORMAT_KEY => 'Y-m-d H:i:s'])]
    private ?\DateTime $dateUpdated = null;

    #[ORM\Column(type: Types::DATETIMETZ_MUTABLE)]
    #[Groups([Product::GROUP_READ, Book::GROUP_BOOK_READ])]
    #[Context([DateTimeNormalizer::FORMAT_KEY => 'Y-m-d H:i:s'])]
    private ?\DateTime $dateCreated = null;

    public function getBook(): Book
    {
        return $this->book;
    }

    public function setBook(Book $book): static
    {
        $this->book = $book;

        return $this;
    }

    public function getReleaseDate(): ?\DateTime
    {
        return $this->releaseDate;
    }

    public function setReleaseDate(?\DateTime $releaseDate): static
    {
        $this->releaseDate = $releaseDate;

        return $this;
    }

    public function getListenPriceValue(): ?int
    {
        return $this->listenPriceValue;
    }

    public function setListenPriceValue(?int $listenPriceValue): static
    {
        $this->listenPriceValue = $listenPriceValue;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    #[Groups([Product::GROUP_READ])]
    #[SerializedName('userId')]
    public function getUserId(): ?int
    {
        return $this->userCreated->getId();
    }

    #[Groups([Product::GROUP_READ])]
    #[SerializedName('bookId')]
    public function getBookId(): ?int
    {
        return $this->book->getId();
    }
}
