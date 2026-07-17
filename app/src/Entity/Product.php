<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model\Operation;
use App\Entity\Trait\DateCreatedTimestampTrait;
use App\Entity\Trait\DateUpdatedTimestampTrait;
use App\Entity\Trait\IdentifierTrait;
use App\Entity\Trait\UserAwareTrait;
use App\Repository\ProductRepository;
use App\State\ProductProvider;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\MaxDepth;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ORM\Table(options: ['comment' => 'Продукт'])]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new Get(
            requirements: ['id' => '\d+'],
            openapi: new Operation(
                summary: 'Получить товар',
            ),
            normalizationContext: [
                'groups' => [self::GROUP_PRODUCT_READ],
                'enable_max_depth' => true,
            ]
        ),
        new GetCollection(
            openapi: new Operation(
                summary: 'Получить список товаров',
            ),
            normalizationContext: ['groups' => [self::GROUP_PRODUCT_READ]],
        ),
        new Post(
            openapi: new Operation(
                summary: 'Создать товар',
            ),
            denormalizationContext: ['groups' => [self::GROUP_PRODUCT_WRITE]],
        ),
        new Patch(
            requirements: ['id' => '\d+'],
            openapi: new Operation(
                summary: 'Обновить товар',
            ),
            denormalizationContext: ['groups' => [self::GROUP_PRODUCT_WRITE]],
        ),
    ],
    order: ['id' => 'DESC'],
    security: "is_granted('ROLE_USER')",
    provider: ProductProvider::class
)]
class Product implements UserAwareInterface
{
    use DateCreatedTimestampTrait;
    use DateUpdatedTimestampTrait;
    use IdentifierTrait;
    use UserAwareTrait;

    public const string GROUP_PRODUCT_READ = 'product:read';
    public const string GROUP_PRODUCT_WRITE = 'product:write';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([self::GROUP_PRODUCT_READ])]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    #[Groups([self::GROUP_PRODUCT_READ, self::GROUP_PRODUCT_WRITE])]
    private ?string $shopProductId = null;

    #[ORM\Column(length: 20, nullable: true)]
    #[Groups([self::GROUP_PRODUCT_READ, self::GROUP_PRODUCT_WRITE])]
    private ?string $shopProductCode = null;

    #[ORM\ManyToOne]
    #[Groups([self::GROUP_PRODUCT_READ])]
    private ?SourceProduct $sourceProduct = null;

    #[ORM\ManyToOne]
    #[Groups([self::GROUP_PRODUCT_READ])]
    private ?Book $book = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([self::GROUP_PRODUCT_READ])]
    private ?Shop $shop = null;

    #[ORM\ManyToOne]
    private ?City $city = null;

    #[ORM\Column(length: 255)]
    #[Groups([self::GROUP_PRODUCT_READ, self::GROUP_PRODUCT_WRITE])]
    private ?string $title = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([self::GROUP_PRODUCT_READ])]
    private ?User $userCreated = null;

    #[ORM\Column]
    #[Groups([self::GROUP_PRODUCT_READ])]
    private ?\DateTimeImmutable $dateUpdated = null;

    #[ORM\Column]
    #[Groups([self::GROUP_PRODUCT_READ])]
    private ?\DateTimeImmutable $dateCreated = null;

    /**
     * @var Collection<int, ProductPrice>
     */
    #[ORM\OneToMany(targetEntity: ProductPrice::class, mappedBy: 'product')]
    #[MaxDepth(1)]
    #[Groups([self::GROUP_PRODUCT_READ])]
    private Collection $prices;

    /**
     * @var Collection<int, ProductStock>
     */
    #[ORM\OneToMany(targetEntity: ProductStock::class, mappedBy: 'product')]
    #[MaxDepth(1)]
    #[Groups([self::GROUP_PRODUCT_READ])]
    private Collection $stocks;

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

    /**
     * @return Collection<int, ProductStock>
     */
    public function getStocks(): Collection
    {
        return $this->stocks;
    }

    public function addStock(ProductStock $productStock): static
    {
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
}
