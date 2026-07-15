<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Trait\DateCreatedTimestampTrait;
use App\Entity\Trait\UserAwareTrait;
use App\Repository\ProductPriceRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: ProductPriceRepository::class)]
#[ORM\Table(options: ['comment' => 'Цена товара'])]
#[ORM\UniqueConstraint(name: 'pp_product_user_date', columns: ['product_id', 'user_created_id', 'date_created_string'])]
#[UniqueEntity(
    fields: ['product_id', 'user_created_id', 'date_created_string'],
    message: 'ProductPrice с такой комбинацией полей уже существует'
)]
#[ORM\HasLifecycleCallbacks]
class ProductPrice implements UserAwareInterface
{
    use DateCreatedTimestampTrait;
    use UserAwareTrait;

    #[ORM\Id]
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $product = null;

    #[ORM\Column]
    private ?int $price = null;

    #[ORM\Id]
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $userCreated = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $dateCreated = null;

    #[ORM\Id]
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

    public function getDateCreatedString(): ?string
    {
        return $this->dateCreatedString;
    }

    public function setDateCreatedString(string $dateCreatedString): static
    {
        $this->dateCreatedString = $dateCreatedString;

        return $this;
    }
}
