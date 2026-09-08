<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Link;
use App\Entity\Trait\DateCreatedTimestampTrait;
use App\Entity\Trait\DateUpdatedTimestampTrait;
use App\Repository\ProductUserDataRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Attribute\Context;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

#[ORM\Entity(repositoryClass: ProductUserDataRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/product_user_data/{product}/{userCreated}',
            uriVariables: [
                'product' => new Link(fromClass: ProductUserData::class, identifiers: ['product.id']),
                'userCreated' => new Link(fromClass: ProductUserData::class, identifiers: ['userCreated.id']),
            ],
        ),
    ],
    security: "is_granted('ROLE_USER')"
)]
#[ORM\UniqueConstraint(name: 'pud_product_user', columns: ['product_id', 'user_created_id'])]
#[UniqueEntity(
    fields: ['product_id', 'user_created_id'],
    message: 'ProductUserData с такой комбинацией полей уже существует'
)]
class ProductUserData
{
    use DateCreatedTimestampTrait;
    use DateUpdatedTimestampTrait;

    public const string GROUP_CREATE = 'product_user_data:write:create';
    public const string GROUP_UPDATE = 'product_user_data:write:update';

    #[ORM\Id]
    #[ApiProperty(identifier: true)]
    #[ORM\OneToOne(targetEntity: Product::class, inversedBy: 'productUserData')]
    #[ORM\JoinColumn(name: 'product_id', referencedColumnName: 'id', nullable: false)]
    private ?Product $product = null;

    #[ORM\Id]
    #[ApiProperty(identifier: true)]
    #[ORM\OneToOne]
    #[ORM\JoinColumn(name: 'user_created_id', referencedColumnName: 'id', nullable: false)]
    private ?User $userCreated = null;

    #[ORM\Column]
    #[Groups([Product::GROUP_READ, self::GROUP_CREATE, self::GROUP_UPDATE])]
    private ?bool $available = null;

    #[ORM\Column(type: Types::DATETIMETZ_MUTABLE, nullable: true)]
    #[Groups([Product::GROUP_READ,  self::GROUP_CREATE,  self::GROUP_UPDATE])]
    private ?\DateTime $notAvailableDateFrom = null;

    #[ORM\Column(type: Types::DATETIMETZ_MUTABLE, nullable: true)]
    #[Groups([Product::GROUP_READ, self::GROUP_CREATE, self::GROUP_UPDATE])]
    private ?\DateTime $availableDateFrom = null;

    #[ORM\Column(nullable: true)]
    #[Groups([Product::GROUP_READ, self::GROUP_CREATE, self::GROUP_UPDATE])]
    private ?int $listenPriceValue = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    #[Groups([Product::GROUP_READ, self::GROUP_CREATE, self::GROUP_UPDATE])]
    private ?int $listenQtyValue = null;

    #[ORM\Column(type: Types::DATETIMETZ_MUTABLE, nullable: true)]
    #[Groups([Product::GROUP_READ, self::GROUP_CREATE, self::GROUP_UPDATE])]
    private ?\DateTime $releaseDate = null;

    #[ORM\Column]
    #[Groups([Product::GROUP_READ, self::GROUP_CREATE, self::GROUP_UPDATE])]
    private ?bool $isArchive = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups([Product::GROUP_READ, self::GROUP_CREATE, self::GROUP_UPDATE])]
    private ?string $comment = null;

    #[ORM\Column(type: Types::DATETIMETZ_MUTABLE)]
    #[Groups([Product::GROUP_READ])]
    #[Context([DateTimeNormalizer::FORMAT_KEY => 'Y-m-d H:i:s'])]
    private ?\DateTime $dateUpdated = null;

    #[ORM\Column(type: Types::DATETIMETZ_MUTABLE)]
    #[Groups([Product::GROUP_READ])]
    #[Context([DateTimeNormalizer::FORMAT_KEY => 'Y-m-d H:i:s'])]
    private ?\DateTime $dateCreated = null;

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;

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

    public function isAvailable(): ?bool
    {
        return $this->available;
    }

    public function setAvailable(bool $available): static
    {
        $this->available = $available;

        return $this;
    }

    public function getNotAvailableDateFrom(): ?\DateTime
    {
        return $this->notAvailableDateFrom;
    }

    public function setNotAvailableDateFrom(?\DateTime $notAvailableDateFrom): static
    {
        $this->notAvailableDateFrom = $notAvailableDateFrom;

        return $this;
    }

    public function getAvailableDateFrom(): ?\DateTime
    {
        return $this->availableDateFrom;
    }

    public function setAvailableDateFrom(?\DateTime $availableDateFrom): static
    {
        $this->availableDateFrom = $availableDateFrom;

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

    public function getListenQtyValue(): ?int
    {
        return $this->listenQtyValue;
    }

    public function setListenQtyValue(?int $listenQtyValue): static
    {
        $this->listenQtyValue = $listenQtyValue;

        return $this;
    }

    public function getReleaseDate(): ?\DateTime
    {
        return $this->releaseDate;
    }

    public function setReleaseDate(?\DateTime $releaseDate): static
    {
        $this->releaseDate = $releaseDate;

        return $this;
    }

    public function isArchive(): ?bool
    {
        return $this->isArchive;
    }

    public function setIsArchive(bool $isArchive): static
    {
        $this->isArchive = $isArchive;

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
