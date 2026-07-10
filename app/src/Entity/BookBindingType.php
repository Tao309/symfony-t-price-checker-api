<?php

namespace App\Entity;

use App\Entity\Trait\IdentifierTrait;
use App\Repository\BookBindingTypeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookBindingTypeRepository::class)]
#[ORM\Table(options: ['comment' => 'Тип переплёта'])]
class BookBindingType
{
    use IdentifierTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $label = null;

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }
}
