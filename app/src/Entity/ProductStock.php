<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Link;
use App\Entity\Trait\DateCreatedStringTrait;
use App\Entity\Trait\DateCreatedTimestampTrait;
use App\Entity\Trait\UserAwareTrait;
use App\Repository\ProductStockRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: ProductStockRepository::class)]
#[ORM\Table(options: ['comment' => 'Сток товара'])]
#[ORM\UniqueConstraint(name: 'ps_product_user_date', columns: ['product_id', 'user_created_id', 'date_created_string'])]
#[UniqueEntity(
    fields: ['product_id', 'user_created_id', 'date_created_string'],
    message: 'ProductStock с такой комбинацией полей уже существует'
)]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/product_stocks/{product}/{userCreated}/{dateCreatedString}',
            uriVariables: [
                'product' => new Link(fromClass: ProductStock::class, identifiers: ['product.id']),
                'userCreated' => new Link(fromClass: ProductStock::class, identifiers: ['userCreated.id']),
                'dateCreatedString' => new Link(fromClass: ProductStock::class, identifiers: ['dateCreatedString']),
            ],
        ),
    ],
    order: ['date_created' => 'ASC'],
    security: "is_granted('ROLE_USER')"
)]
class ProductStock implements UserAwareInterface
{
    use DateCreatedStringTrait;
    use DateCreatedTimestampTrait;
    use UserAwareTrait;

    #[ORM\Id]
    #[ApiProperty(identifier: true)]
    #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: 'stocks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $product = null;

    #[ORM\Column]
    #[Groups([Product::GROUP_PRODUCT_READ])]
    private ?int $qty = null;

    #[ORM\Column]
    #[Groups([Product::GROUP_PRODUCT_READ])]
    private ?\DateTimeImmutable $dateCreated = null;

    #[ORM\Id]
    #[ApiProperty(identifier: true)]
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $userCreated = null;

    #[ORM\Column(nullable: true)]
    #[Groups([Product::GROUP_PRODUCT_READ])]
    private ?array $log = null;

    #[ORM\Id]
    #[ApiProperty(identifier: true)]
    #[ORM\Column(length: 25)]
    private ?string $dateCreatedString = null;

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;

        return $this;
    }

    public function getQty(): ?int
    {
        return $this->qty;
    }

    public function setQty(int $qty): static
    {
        $this->qty = $qty;

        return $this;
    }

    public function getLog(): ?array
    {
        return $this->log;
    }

    public function setLog(?array $log): static
    {
        $this->log = $log;

        return $this;
    }
}
