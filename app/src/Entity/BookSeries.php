<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\OpenApi\Model\Operation;
use App\Entity\Trait\DateCreatedTimestampTrait;
use App\Entity\Trait\DateUpdatedTimestampTrait;
use App\Repository\BookSeriesRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: BookSeriesRepository::class)]
#[ORM\Table(options: ['comment' => 'Серия'])]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new Get(
            requirements: ['id' => '\d+'],
            openapi: new Operation(
                summary: 'Получить серию книги',
            ),
            normalizationContext: ['groups' => [Book::GROUP_BOOK_READ]],
        ),
    ],
    order: ['id' => 'DESC'],
    security: "is_granted('ROLE_USER')"
)]
class BookSeries
{
    use DateUpdatedTimestampTrait, DateCreatedTimestampTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([Book::GROUP_BOOK_READ])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups([Book::GROUP_BOOK_READ, Book::GROUP_BOOK_WRITE])]
    private ?string $name = null;

    #[ORM\Column]
    #[Groups([Book::GROUP_BOOK_READ])]
    private ?\DateTimeImmutable $dateUpdated = null;

    #[ORM\Column]
    #[Groups([Book::GROUP_BOOK_READ])]
    private ?\DateTimeImmutable $dateCreated = null;

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
}
