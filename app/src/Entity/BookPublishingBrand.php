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
use App\Repository\BookPublishingBrandRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BookPublishingBrandRepository::class)]
#[ORM\Table(options: ['comment' => 'Издательский брэнд'])]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new Post(
            openapi: new Operation(
                summary: 'Создать издательский брэнд',
            ),
            denormalizationContext: ['groups' => [self::GROUP_BPB_WRITE]],
        ),
        new Patch(
            requirements: ['id' => '\d+'],
            openapi: new Operation(
                summary: 'Обновить издательский брэнд',
            ),
            denormalizationContext: ['groups' => [self::GROUP_BPB_WRITE]],
        ),
    ],
    order: ['id' => 'DESC'],
    security: "is_granted('ROLE_USER')"
)]
class BookPublishingBrand
{
    use DateCreatedTimestampTrait;
    use DateUpdatedTimestampTrait;
    use IdentifierTrait;

    public const string GROUP_BPB_WRITE = 'book_publishing_brand:write';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([Book::GROUP_BOOK_READ, Product::GROUP_PRODUCT_READ])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull(groups: [self::GROUP_BPB_WRITE])]
    #[Groups([self::GROUP_BPB_WRITE, Book::GROUP_BOOK_READ, Book::GROUP_BOOK_WRITE, Product::GROUP_PRODUCT_READ])]
    private ?string $name = null;

    #[ORM\Column]
    #[Groups([Book::GROUP_BOOK_READ, Product::GROUP_PRODUCT_READ])]
    private ?\DateTimeImmutable $dateUpdated = null;

    #[ORM\Column]
    #[Groups([Book::GROUP_BOOK_READ, Product::GROUP_PRODUCT_READ])]
    private ?\DateTimeImmutable $dateCreated = null;

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
