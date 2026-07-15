<?php

declare(strict_types=1);

namespace App\Entity\Trait;

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
