<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Trait\DateCreatedTimestampTrait;
use App\Entity\Trait\DateUpdatedTimestampTrait;
use App\Repository\BookUserDataRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: BookUserDataRepository::class)]
#[ORM\UniqueConstraint(name: 'bud_book_user', columns: ['book_id', 'user_created_id'])]
#[ORM\Table(options: ['comment' => 'Пользовательские данные по книгам'])]
#[UniqueEntity(
    fields: ['book_id', 'user_created_id'],
    message: 'BookUserData с такой комбинацией полей уже существует'
)]
#[ORM\HasLifecycleCallbacks]
class BookUserData
{
    use DateCreatedTimestampTrait;
    use DateUpdatedTimestampTrait;

    #[ORM\Id]
    #[ORM\ManyToOne]
    private ?Book $book;

    #[ORM\Id]
    #[ORM\ManyToOne]
    private ?User $userCreated;

    #[ORM\Column]
    private ?\DateTimeImmutable $releaseDate = null;

    #[ORM\Column(nullable: true)]
    private ?int $listenPriceValue = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $comment = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $dateUpdated = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $dateCreated = null;

    public function getBook(): Book
    {
        return $this->book;
    }

    public function setBook(Book $book): static
    {
        $this->book = $book;

        return $this;
    }

    public function getUserCreated(): ?User
    {
        return $this->userCreated;
    }

    public function setUserCreated(User $userCreated): static
    {
        $this->userCreated = $userCreated;

        return $this;
    }

    public function getReleaseDate(): ?\DateTimeImmutable
    {
        return $this->releaseDate;
    }

    public function setReleaseDate(\DateTimeImmutable $releaseDate): static
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
}
