<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use App\Entity\Trait\DateCreatedStringTrait;
use App\Entity\Trait\DateCreatedTimestampTrait;
use App\Entity\Trait\UserAwareTrait;
use App\Repository\ProductStockRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Attribute\Context;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

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
        new GetCollection(openapi: false),
        new Get(
            uriTemplate: '/product_stocks/{product}/{userCreated}/{dateCreatedString}',
            uriVariables: [
                'product' => new Link(fromClass: ProductStock::class, identifiers: ['product.id']),
                'userCreated' => new Link(fromClass: ProductStock::class, identifiers: ['userCreated.id']),
                'dateCreatedString' => new Link(fromClass: ProductStock::class, identifiers: ['dateCreatedString']),
            ],
            normalizationContext: ['groups' => [self::GROUP_READ]],
        ),
        new Delete(
            uriTemplate: '/product_stocks/{product}/{qty}/{dateCreatedString}',
            uriVariables: [
                'product' => new Link(fromClass: ProductStock::class, identifiers: ['product']),
                'qty' => new Link(fromClass: ProductStock::class, identifiers: ['qty']),
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

    public const string GROUP_READ = 'product_stock:read';
    public const string GROUP_WRITE = 'product_stock:write';

    #[ORM\Id]
    #[ApiProperty(identifier: true)]
    #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: 'stocks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $product = null;

    #[ORM\Column]
    #[Groups([Product::GROUP_READ, self::GROUP_WRITE, self::GROUP_READ])]
    private ?int $qty = null;

    #[ORM\Column(type: Types::DATETIMETZ_MUTABLE)]
    #[Groups([Product::GROUP_READ, self::GROUP_READ])]
    #[SerializedName('date')]
    #[Context([DateTimeNormalizer::FORMAT_KEY => 'Y-m-d H:i:s'])]
    private ?\DateTime $dateCreated = null;

    #[ORM\Id]
    #[ApiProperty(identifier: true)]
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $userCreated = null;

    #[ORM\Column(nullable: true)]
    #[Groups([Product::GROUP_READ, self::GROUP_WRITE, self::GROUP_READ])]
    private ?array $log = null;

    #[ORM\Id]
    #[ApiProperty(identifier: true)]
    #[ORM\Column(length: 50, nullable: false)]
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
