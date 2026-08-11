<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\QueryParameter;
use ApiPlatform\OpenApi\Model;
use ApiPlatform\OpenApi\Model\Operation;
use App\Entity\Trait\DateCreatedTimestampTrait;
use App\Entity\Trait\DateUpdatedTimestampTrait;
use App\Entity\Trait\IdentifierTrait;
use App\Entity\Trait\UserAwareTrait;
use App\Repository\ProductRepository;
use App\State\ProductProvider;
use App\State\SaveProductProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Serializer\Attribute\MaxDepth;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ORM\Table(options: ['comment' => 'Продукт'])]
#[ORM\Index(name: 'idx_product_shop_id_product_id', fields: ['shop', 'shopProduct'])]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new Get(
            requirements: ['id' => '\d+'],
            openapi: new Operation(
                summary: 'Получить товар',
            ),
            normalizationContext: [
                'groups' => [self::GROUP_READ],
                'enable_max_depth' => true,
            ],
            provider: ProductProvider::class
        ),
        new GetCollection(
            uriVariables: [],
            openapi: new Operation(
                summary: 'Получить список товаров',
                parameters: [
                    new Model\Parameter(
                        name: 'ids',
                        in: 'path',
                        required: true,
                        schema: ['type' => 'string']
                    ),
                ]
            ),
            normalizationContext: ['groups' => [self::GROUP_READ]],
            provider: ProductProvider::class,
            parameters: [
                'ids' => new QueryParameter(
                    required: true
                ),
            ]
        ),
        new Post(
            inputFormats: ['json' => ['application/json']],
            openapi: new Operation(
                responses: [
                    200 => new Model\Response(
                        description: 'Успешный ответ',
                        content: new \ArrayObject([
                            'application/ld+json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'product' => [
                                            '$ref' => '#/components/schemas/Product.jsonld',
                                        ],
                                    ],
                                ],
                            ],
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'product' => [
                                            '$ref' => '#/components/schemas/Product',
                                        ],
                                    ],
                                ],
                            ],
                        ])
                    ),
                ],
                summary: 'Создать товар',
            ),
            normalizationContext: ['groups' => [self::GROUP_READ]],
            denormalizationContext: [
                'groups' => [
                    self::GROUP_CREATE,
                    ProductUserData::GROUP_CREATE,
                ],
            ],
            validationContext: [
                'groups' => [self::GROUP_CREATE],
            ],
            name: 'create_product',
            provider: ProductProvider::class,
            processor: SaveProductProcessor::class,
        ),
        new Patch(
            inputFormats: ['json' => ['application/json']],
            requirements: ['id' => '\d+'],
            openapi: new Operation(
                responses: [
                    200 => new Model\Response(
                        description: 'Успешный ответ',
                        content: new \ArrayObject([
                            'application/ld+json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'product' => [
                                            '$ref' => '#/components/schemas/Product.jsonld',
                                        ],
                                    ],
                                ],
                            ],
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'product' => [
                                            '$ref' => '#/components/schemas/Product',
                                        ],
                                    ],
                                ],
                            ],
                        ])
                    ),
                ],
                summary: 'Обновить товар',
            ),
            normalizationContext: ['groups' => [self::GROUP_READ]],
            denormalizationContext: [
                'groups' => [
                    self::GROUP_UPDATE,
                    ProductUserData::GROUP_UPDATE,
                ],
            ],
            validationContext: [
                'groups' => [self::GROUP_UPDATE],
            ],
            provider: ProductProvider::class,
            processor: SaveProductProcessor::class,
        ),
        new Post(
            uriTemplate: '/products/archive',
            uriVariables: [],
            openapi: new Operation(
                summary: 'Архивировать товар',
            ),
            normalizationContext: ['groups' => [self::GROUP_READ]],
            denormalizationContext: ['groups' => [self::GROUP_CREATE, ProductUserData::GROUP_CREATE]],
            deserialize: true,
            name: 'archive_product',
            provider: ProductProvider::class,
            processor: SaveProductProcessor::class,
        ),
    ],
    order: ['id' => 'DESC'],
    security: "is_granted('ROLE_USER')",
)]
#[ApiFilter(SearchFilter::class, properties: [
    'shopProductId' => 'exact',
    'dates.userCreated.id' => 'exact',
])]
class Product implements UserAwareInterface
{
    use DateCreatedTimestampTrait;
    use DateUpdatedTimestampTrait;
    use IdentifierTrait;
    use UserAwareTrait;

    public const string GROUP_READ = 'product:read';
    public const string GROUP_CREATE = 'product:write:create';
    public const string GROUP_AFTER_CREATE = 'product:write:after_create';
    public const string GROUP_UPDATE = 'product:write:update';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([self::GROUP_READ])]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    #[SerializedName('shop_product_id')]
    #[Groups([self::GROUP_READ, self::GROUP_CREATE])]
    #[Assert\NotBlank(groups: [self::GROUP_CREATE, self::GROUP_UPDATE])]
    private ?string $shopProductId = null;

    #[ORM\Column(length: 20, nullable: true)]
    #[SerializedName('shop_product_code')]
    #[Groups([self::GROUP_READ, self::GROUP_CREATE])]
    private ?string $shopProductCode = null;

    #[ORM\ManyToOne]
    #[Groups([self::GROUP_READ, self::GROUP_CREATE])]
    private ?SourceProduct $sourceProduct = null;

    #[ORM\ManyToOne]
    #[Groups([self::GROUP_READ, self::GROUP_CREATE])]
    private ?Book $book = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([self::GROUP_READ, self::GROUP_CREATE])]
    #[Assert\NotBlank(groups: [self::GROUP_CREATE, self::GROUP_UPDATE])]
    private ?Shop $shop = null;

    #[ORM\ManyToOne]
    private ?City $city = null;

    #[ORM\Column(length: 255)]
    #[Groups([self::GROUP_READ, self::GROUP_CREATE, self::GROUP_UPDATE])]
    #[Assert\NotBlank(groups: [self::GROUP_CREATE, self::GROUP_UPDATE])]
    private ?string $title = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([self::GROUP_READ])]
    private ?User $userCreated = null;

    #[ORM\Column(type: Types::DATETIMETZ_MUTABLE)]
    #[Groups([self::GROUP_READ])]
    private ?\DateTime $dateUpdated = null;

    #[ORM\Column(type: Types::DATETIMETZ_MUTABLE)]
    #[Groups([self::GROUP_READ])]
    private ?\DateTime $dateCreated = null;

    /**
     * @var Collection<int, ProductPrice>
     */
    #[ORM\OneToMany(
        targetEntity: ProductPrice::class,
        mappedBy: 'product',
        cascade: ['persist', 'refresh'],
        orphanRemoval: true,
    )]
    #[MaxDepth(1)]
    #[Groups([self::GROUP_READ])]
    #[Assert\Count(
        min: 1,
        minMessage: 'You must add at least one item to the collection.',
        groups: [self::GROUP_AFTER_CREATE]
    )]
    private Collection $prices;

    /**
     * @var Collection<int, ProductStock>
     */
    #[ORM\OneToMany(
        targetEntity: ProductStock::class,
        mappedBy: 'product',
        cascade: ['persist', 'refresh'],
        orphanRemoval: true,
    )]
    #[MaxDepth(1)]
    #[Groups([self::GROUP_READ])]
    #[Assert\Count(
        min: 1,
        minMessage: 'You must add at least one item to the collection.',
        groups: [self::GROUP_AFTER_CREATE]
    )]
    private Collection $stocks;

    #[ORM\OneToOne(targetEntity: ProductUserData::class, mappedBy: 'product', cascade: ['persist', 'refresh'])]
    #[MaxDepth(1)]
    #[Groups([self::GROUP_READ, ProductUserData::GROUP_UPDATE, ProductUserData::GROUP_CREATE])]
    #[SerializedName('product_user_data')]
    #[Assert\NotBlank(groups: [self::GROUP_CREATE])]
    private ?ProductUserData $productUserData = null;

    public function __construct()
    {
        $this->prices = new ArrayCollection();
        $this->stocks = new ArrayCollection();
    }

    public function getShopProductId(): ?string
    {
        return $this->shopProductId;
    }

    public function setShopProductId(string $shopProductId): static
    {
        $this->shopProductId = $shopProductId;

        return $this;
    }

    public function getShopProductCode(): ?string
    {
        return $this->shopProductCode;
    }

    public function setShopProductCode(?string $shopProductCode): static
    {
        $this->shopProductCode = $shopProductCode;

        return $this;
    }

    public function getSourceProduct(): ?SourceProduct
    {
        return $this->sourceProduct;
    }

    public function setSourceProduct(?SourceProduct $sourceProduct): static
    {
        $this->sourceProduct = $sourceProduct;

        return $this;
    }

    public function getBook(): ?Book
    {
        return $this->book;
    }

    public function setBook(?Book $book): static
    {
        $this->book = $book;

        return $this;
    }

    public function getShop(): ?Shop
    {
        return $this->shop;
    }

    public function setShop(?Shop $shop): static
    {
        $this->shop = $shop;

        return $this;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(?City $city): static
    {
        $this->city = $city;

        return $this;
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

    /**
     * @return Collection<int, ProductPrice>
     */
    public function getPrices(): Collection
    {
        return $this->prices;
    }

    public function addPrice(ProductPrice $productPrice): static
    {
        foreach ($this->prices as $price) {
            if ($price->getDateCreatedString() === $productPrice->getDateCreatedString()
            ) {
                $price->setPrice($productPrice->getPrice());

                return $this;
            }
        }

        if (!$this->prices->contains($productPrice)) {
            $this->prices->add($productPrice);
            $productPrice->setProduct($this);
        }

        return $this;
    }

    public function removePrice(ProductPrice $productPrice): static
    {
        if ($this->prices->removeElement($productPrice)) {
            if ($productPrice->getProduct() === $this) {
                $productPrice->setProduct(null);
            }
        }

        return $this;
    }

    #[Ignore]
    public function getMinPrice(): ?int
    {
        return min(
            array_map(static fn ($priceDate) => $priceDate->getPrice(), $this->getPrices()->toArray())
        );
    }

    /**
     * @return Collection<int, ProductStock>
     */
    public function getStocks(): Collection
    {
        return $this->stocks;
    }

    public function addStock(ProductStock $productStock): static
    {
        foreach ($this->stocks as $stock) {
            if ($stock->getDateCreatedString() === $productStock->getDateCreatedString()
            ) {
                $stock->setQty($productStock->getQty());
                $stock->setLog($productStock->getLog());

                return $this;
            }
        }

        if (!$this->stocks->contains($productStock)) {
            $this->stocks->add($productStock);
            $productStock->setProduct($this);
        }

        return $this;
    }

    public function removeStock(ProductStock $productStock): static
    {
        if ($this->stocks->removeElement($productStock)) {
            if ($productStock->getProduct() === $this) {
                $productStock->setProduct(null);
            }
        }

        return $this;
    }

    #[Ignore]
    public function getLastQty(): ?int
    {
        return $this->getStocks()->last()->getQty();
    }

    #[Ignore]
    public function getLastStock(): ?ProductStock
    {
        return $this->getStocks()->last();
    }

    public function getProductUserData(): ?ProductUserData
    {
        return $this->productUserData;
    }

    public function setProductUserData(?ProductUserData $productUserData): static
    {
        $this->productUserData = $productUserData;

        return $this;
    }
}
