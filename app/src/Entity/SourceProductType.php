<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\OpenApi\Model\Operation;
use App\Entity\Trait\DateCreatedTimestampTrait;
use App\Entity\Trait\DateUpdatedTimestampTrait;
use App\Entity\Trait\IdentifierTrait;
use App\Repository\SourceProductTypeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: SourceProductTypeRepository::class)]
#[ORM\Table(options: ['comment' => 'Тип источника товара'])]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new Get(
            requirements: ['id' => '\d+'],
            openapi: new Operation(
                summary: 'Получить тип источника товара',
            ),
            normalizationContext: ['groups' => [Product::GROUP_PRODUCT_READ]],
        ),
    ],
    order: ['id' => 'DESC'],
    security: "is_granted('ROLE_USER')"
)]
class SourceProductType
{
    use DateCreatedTimestampTrait;
    use DateUpdatedTimestampTrait;
    use IdentifierTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([Product::GROUP_PRODUCT_READ])]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Groups([Product::GROUP_PRODUCT_READ])]
    private ?string $code = null;

    #[ORM\Column(length: 50)]
    #[Groups([Product::GROUP_PRODUCT_READ])]
    private ?string $name = null;

    #[ORM\Column]
    #[Groups([Product::GROUP_PRODUCT_READ])]
    private ?\DateTimeImmutable $dateUpdated = null;

    #[ORM\Column]
    #[Groups([Product::GROUP_PRODUCT_READ])]
    private ?\DateTimeImmutable $dateCreated = null;

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
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
