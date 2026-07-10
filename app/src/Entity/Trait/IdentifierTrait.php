<?php

namespace App\Entity\Trait;

use Doctrine\ORM\Mapping as ORM;

trait IdentifierTrait
{
    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }
}
