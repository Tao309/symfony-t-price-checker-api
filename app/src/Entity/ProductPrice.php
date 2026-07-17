<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use App\Entity\Trait\DateCreatedStringTrait;
use App\Entity\Trait\DateCreatedTimestampTrait;
use App\Entity\Trait\UserAwareTrait;
use App\Repository\ProductPriceRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: ProductPriceRepository::class)]
#[ORM\Table(options: ['comment' => 'Цена товара'])]
#[ORM\UniqueConstraint(name: 'pp_product_user_date', columns: ['product_id', 'user_created_id', 'date_created_string'])]
#[UniqueEntity(
    fields: ['product_id', 'user_created_id', 'date_created_string'],
    message: 'ProductPrice с такой комбинацией полей уже существует'
)]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(
            uriTemplate: '/product_prices/{product}/{userCreated}/{dateCreatedString}',
            uriVariables: [
                'product' => new Link(fromClass: ProductPrice::class, identifiers: ['product.id']),
                'userCreated' => new Link(fromClass: ProductPrice::class, identifiers: ['userCreated.id']),
                'dateCreatedString' => new Link(fromClass: ProductPrice::class, identifiers: ['dateCreatedString']),
            ],
        ),
    ],
    order: ['date_created' => 'ASC'],
    security: "is_granted('ROLE_USER')",
)]
#[ApiFilter(SearchFilter::class, properties: ['userCreated.id' => 'exact'])]
class ProductPrice implements UserAwareInterface
{
    use DateCreatedStringTrait;
    use DateCreatedTimestampTrait;
    use UserAwareTrait;

    #[ORM\Id]
    #[ApiProperty(identifier: true)]
    #[ORM\ManyToOne(inversedBy: 'prices')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $product = null;

    #[ORM\Column]
    #[Groups([Product::GROUP_PRODUCT_READ])]
    private ?int $price = null;

    #[ORM\Id]
    #[ApiProperty(identifier: true)]
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $userCreated = null;

    #[ORM\Column]
    #[Groups([Product::GROUP_PRODUCT_READ])]
    private ?\DateTimeImmutable $dateCreated = null;

    #[ORM\Id]
    #[ApiProperty(identifier: true)]
    #[ORM\Column(length: 25, nullable: false)]
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

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): static
    {
        $this->price = $price;

        return $this;
    }
}
