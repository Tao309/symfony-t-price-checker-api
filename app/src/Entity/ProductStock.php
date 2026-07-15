<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Trait\DateCreatedTimestampTrait;
use App\Entity\Trait\UserAwareTrait;
use App\Repository\ProductStockRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: ProductStockRepository::class)]
#[ORM\Table(options: ['comment' => 'Сток товара'])]
#[ORM\UniqueConstraint(name: 'ps_product_user_date', columns: ['product_id', 'user_created_id', 'date_created_string'])]
#[UniqueEntity(
    fields: ['product_id', 'user_created_id', 'date_created_string'],
    message: 'ProductStock с такой комбинацией полей уже существует'
)]
#[ORM\HasLifecycleCallbacks]
class ProductStock implements UserAwareInterface
{
    use DateCreatedTimestampTrait;
    use UserAwareTrait;

    #[ORM\Id]
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $product = null;

    #[ORM\Column]
    private ?int $qty = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $dateCreated = null;

    #[ORM\Id]
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $userCreated = null;

    #[ORM\Column(nullable: true)]
    private ?array $log = null;

    #[ORM\Id]
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
