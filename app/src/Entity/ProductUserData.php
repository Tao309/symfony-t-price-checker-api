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
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\SerializedName;

#[ORM\Entity(repositoryClass: ProductUserDataRepository::class)]
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

    public const string GROUP_PUD_WRITE_UPDATE = 'product_user_data:write:update';

    #[ORM\Id]
    #[ApiProperty(identifier: true)]
    #[ORM\OneToOne(targetEntity: Product::class, inversedBy: 'productUserData')]
    private ?Product $product = null;

    #[ORM\Id]
    #[ApiProperty(identifier: true)]
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[SerializedName('user')]
    private ?User $userCreated = null;

    #[ORM\Column]
    #[Groups([Product::GROUP_PRODUCT_READ, self::GROUP_PUD_WRITE_UPDATE])]
    #[SerializedName('available')]
    private ?bool $available = null;

    #[ORM\Column(type: Types::DATETIMETZ_MUTABLE, nullable: true)]
    #[Groups([Product::GROUP_PRODUCT_READ,  self::GROUP_PUD_WRITE_UPDATE])]
    #[SerializedName('not_available_date_from')]
    private ?\DateTime $notAvailableDateFrom = null;

    #[ORM\Column(type: Types::DATETIMETZ_MUTABLE, nullable: true)]
    #[Groups([Product::GROUP_PRODUCT_READ, self::GROUP_PUD_WRITE_UPDATE])]
    #[SerializedName('available_date_from')]
    private ?\DateTime $availableDateFrom = null;

    #[ORM\Column(nullable: true)]
    #[Groups([Product::GROUP_PRODUCT_READ, self::GROUP_PUD_WRITE_UPDATE])]
    #[SerializedName('listen_price_value')]
    private ?int $listenPriceValue = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    #[Groups([Product::GROUP_PRODUCT_READ, self::GROUP_PUD_WRITE_UPDATE])]
    #[SerializedName('listen_qty_value')]
    private ?int $listenQtyValue = null;

    #[ORM\Column(type: Types::DATETIMETZ_MUTABLE, nullable: true)]
    #[Groups([Product::GROUP_PRODUCT_READ, self::GROUP_PUD_WRITE_UPDATE])]
    #[SerializedName('release_date')]
    private ?\DateTime $releaseDate = null;

    #[ORM\Column]
    #[Groups([Product::GROUP_PRODUCT_READ, self::GROUP_PUD_WRITE_UPDATE])]
    #[SerializedName('is_archive')]
    private ?bool $isArchive = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups([Product::GROUP_PRODUCT_READ, self::GROUP_PUD_WRITE_UPDATE])]
    #[SerializedName('comment')]
    private ?string $comment = null;

    #[ORM\Column(type: Types::DATETIMETZ_MUTABLE)]
    #[Groups([Product::GROUP_PRODUCT_READ])]
    #[SerializedName('date_updated')]
    private ?\DateTime $dateUpdated = null;

    #[ORM\Column(type: Types::DATETIMETZ_MUTABLE)]
    #[Groups([Product::GROUP_PRODUCT_READ])]
    #[SerializedName('date_created')]
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
