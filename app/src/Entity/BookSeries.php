<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model\Operation;
use App\Entity\Trait\DateCreatedTimestampTrait;
use App\Entity\Trait\DateUpdatedTimestampTrait;
use App\Entity\Trait\IdentifierTrait;
use App\Repository\BookSeriesRepository;
use App\State\BookSeriesProcessor;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Attribute\Context;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BookSeriesRepository::class)]
#[ORM\Table(options: ['comment' => 'Книжная серия'])]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields: ['name'], message: 'BookSeries с таким названием уже существует')]
#[ApiResource(
    operations: [
        new Post(
            openapi: new Operation(
                summary: 'Создать книжную серию',
            ),
            denormalizationContext: ['groups' => [self::GROUP_BOOK_SERIES_WRITE]],
            processor: BookSeriesProcessor::class,
        ),
        new Patch(
            requirements: ['id' => '\d+'],
            openapi: new Operation(
                summary: 'Обновить книжную серию',
            ),
            denormalizationContext: ['groups' => [self::GROUP_BOOK_SERIES_WRITE]],
            processor: BookSeriesProcessor::class,
        ),
    ],
    order: ['id' => 'DESC'],
    security: "is_granted('ROLE_USER')"
)]
class BookSeries
{
    use DateCreatedTimestampTrait;
    use DateUpdatedTimestampTrait;
    use IdentifierTrait;

    public const string GROUP_BOOK_SERIES_WRITE = 'book_series:write';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([Book::GROUP_BOOK_READ, Product::GROUP_READ])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull(groups: [self::GROUP_BOOK_SERIES_WRITE])]
    #[Groups([
        self::GROUP_BOOK_SERIES_WRITE,
        Book::GROUP_BOOK_READ, Book::GROUP_BOOK_WRITE, Product::GROUP_READ,
    ])]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATETIMETZ_MUTABLE)]
    #[Groups([Book::GROUP_BOOK_READ, Product::GROUP_READ])]
    #[Context([DateTimeNormalizer::FORMAT_KEY => 'Y-m-d H:i:s'])]
    private ?\DateTime $dateUpdated = null;

    #[ORM\Column(type: Types::DATETIMETZ_MUTABLE)]
    #[Groups([Book::GROUP_BOOK_READ, Product::GROUP_READ])]
    #[Context([DateTimeNormalizer::FORMAT_KEY => 'Y-m-d H:i:s'])]
    private ?\DateTime $dateCreated = null;

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
