<?php

namespace App\Entity;

use App\Repository\BookBindingTypeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookBindingTypeRepository::class)]
#[ORM\Table(options: ['comment' => 'Тип переплёта'])]
#[ORM\HasLifecycleCallbacks]
class BookBindingType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $label = null;

    public function getId(): ?int
    {
        return $this->id;
    }

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
