<?php

namespace App\Entity;

use App\Entity\Trait\DateCreatedTimestampTrait;
use App\Entity\Trait\DateUpdatedTimestampTrait;
use App\Repository\ProductUserDataRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: ProductUserDataRepository::class)]
#[ORM\UniqueConstraint(name: 'spud_source_product_user', columns: ['source_product_id', 'user_created_id'])]
#[ORM\Table(options: ['comment' => 'Пользовательские данные по источникам товара'])]
#[UniqueEntity(
    fields: ['source_product_id', 'user_created_id'],
    message: 'SourceProductUserData с такой комбинацией полей уже существует')]
#[ORM\HasLifecycleCallbacks]
class SourceProductUserData
{
    use DateUpdatedTimestampTrait;
    use DateCreatedTimestampTrait;

    #[ORM\Id]
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?SourceProduct $sourceProduct = null;

    #[ORM\Id]
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $userCreated = null;

    #[ORM\Column(nullable: true)]
    private ?int $listenPriceValue = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $comment = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $dateUpdated = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $dateCreated = null;

    public function getSourceProduct(): ?SourceProduct
    {
        return $this->sourceProduct;
    }

    public function setSourceProduct(?SourceProduct $sourceProduct): static
    {
        $this->sourceProduct = $sourceProduct;

        return $this;
    }

    public function getUserCreated(): ?User
    {
        return $this->userCreated;
    }

    public function setUserCreated(?User $userCreated): static
    {
        $this->userCreated = $userCreated;

        return $this;
    }

    public function getListenPriceValue(): ?int
    {
        return $this->listenPriceValue;
    }

    public function setListenPriceValue(?int $listenPriceValue): static
    {
        $this->listenPriceValue = $listenPriceValue;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }
}
