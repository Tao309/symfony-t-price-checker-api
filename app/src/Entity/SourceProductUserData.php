<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Link;
use App\Entity\Trait\DateCreatedTimestampTrait;
use App\Entity\Trait\DateUpdatedTimestampTrait;
use App\Repository\SourceProductUserDataRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: SourceProductUserDataRepository::class)]
#[ORM\UniqueConstraint(name: 'spud_source_product_user', columns: ['source_product_id', 'user_created_id'])]
#[ORM\Table(options: ['comment' => 'Пользовательские данные по источникам товара'])]
#[UniqueEntity(
    fields: ['source_product_id', 'user_created_id'],
    message: 'SourceProductUserData с такой комбинацией полей уже существует'
)]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/source_product_user_data/{source_product}/{user_created}',
            uriVariables: [
                'source_product' => new Link(fromClass: SourceProductUserData::class, identifiers: ['sourceProduct.id']),
                'user_created' => new Link(fromClass: SourceProductUserData::class, identifiers: ['userCreated.id']),
            ],
        ),
    ],
    security: "is_granted('ROLE_USER')"
)]
class SourceProductUserData
{
    use DateCreatedTimestampTrait;
    use DateUpdatedTimestampTrait;

    #[ORM\Id]
    #[ApiProperty(identifier: true)]
    #[ORM\OneToOne(targetEntity: SourceProduct::class, inversedBy: 'sourceProductUserData')]
    #[ORM\JoinColumn(name: 'source_product_id', referencedColumnName: 'id', nullable: false)]
    private ?SourceProduct $sourceProduct = null;

    #[ORM\Id]
    #[ApiProperty(identifier: true)]
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_created_id', referencedColumnName: 'id', nullable: false)]
    #[Groups([Product::GROUP_READ, SourceProduct::GROUP_SOURCE_PRODUCT_READ])]
    private ?User $userCreated = null;

    #[ORM\Column(nullable: true)]
    #[Groups([Product::GROUP_READ, SourceProduct::GROUP_SOURCE_PRODUCT_READ])]
    private ?int $listenPriceValue = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups([Product::GROUP_READ, SourceProduct::GROUP_SOURCE_PRODUCT_READ])]
    private ?string $comment = null;

    #[ORM\Column(type: Types::DATETIMETZ_MUTABLE)]
    #[Groups([Product::GROUP_READ, SourceProduct::GROUP_SOURCE_PRODUCT_READ])]
    private ?\DateTime $dateUpdated = null;

    #[ORM\Column(type: Types::DATETIMETZ_MUTABLE)]
    #[Groups([Product::GROUP_READ, SourceProduct::GROUP_SOURCE_PRODUCT_READ])]
    private ?\DateTime $dateCreated = null;

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
