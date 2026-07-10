<?php

namespace App\Entity;

use App\Entity\Trait\DateCreatedTimestampTrait;
use App\Entity\Trait\DateUpdatedTimestampTrait;
use App\Entity\Trait\UserAwareTrait;
use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ORM\Table(options: ['comment' => 'Продукт'])]
#[ORM\HasLifecycleCallbacks]
class Product implements UserAwareInterface
{
    use DateUpdatedTimestampTrait, DateCreatedTimestampTrait, UserAwareTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    private ?string $shopProductId = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $shopProductCode = null;

    #[ORM\ManyToOne]
    private ?SourceProduct $sourceProduct = null;

    #[ORM\ManyToOne]
    private ?Book $book = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Shop $shop = null;

    #[ORM\ManyToOne]
    private ?City $city = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $userCreated = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $dateUpdated = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $dateCreated = null;

    public function getId(): ?int
    {
        return $this->id;
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
}
