<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Trait\DateCreatedTimestampTrait;
use App\Entity\Trait\DateUpdatedTimestampTrait;
use App\Entity\Trait\IdentifierTrait;
use App\Repository\SourceProductRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SourceProductRepository::class)]
#[ORM\Table(options: ['comment' => 'Источник товара'])]
#[ORM\HasLifecycleCallbacks]
class SourceProduct
{
    use DateCreatedTimestampTrait;
    use DateUpdatedTimestampTrait;
    use IdentifierTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?SourceProductType $sourceProductType = null;

    #[ORM\Column(length: 100)]
    private ?string $title = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $userCreated = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $dateUpdated = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $dateCreated = null;

    public function getSourceProductType(): ?SourceProductType
    {
        return $this->sourceProductType;
    }

    public function setSourceProductType(?SourceProductType $sourceProductType): static
    {
        $this->sourceProductType = $sourceProductType;

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

    public function getUserCreated(): ?User
    {
        return $this->userCreated;
    }

    public function setUserCreated(?User $userCreated): static
    {
        $this->userCreated = $userCreated;

        return $this;
    }
}
