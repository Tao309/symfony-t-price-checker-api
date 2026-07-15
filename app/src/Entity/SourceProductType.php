<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Trait\DateCreatedTimestampTrait;
use App\Entity\Trait\DateUpdatedTimestampTrait;
use App\Entity\Trait\IdentifierTrait;
use App\Repository\SourceProductTypeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SourceProductTypeRepository::class)]
#[ORM\Table(options: ['comment' => 'Тип источника товара'])]
#[ORM\HasLifecycleCallbacks]
class SourceProductType
{
    use DateCreatedTimestampTrait;
    use DateUpdatedTimestampTrait;
    use IdentifierTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $code = null;

    #[ORM\Column(length: 50)]
    private ?string $name = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $dateUpdated = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $dateCreated = null;

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }
}
