<?php

namespace App\Entity\Trait;

use Doctrine\ORM\Mapping as ORM;

trait DateCreatedTimestampTrait
{
    public function getDateCreated(): ?\DateTimeImmutable
    {
        return $this->dateCreated;
    }

    public function setDateCreated(\DateTimeImmutable $dateCreated): static
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    #[ORM\PrePersist]
    public function setDateCreatedValue(): void
    {
        if ($this->dateCreated === null) {
            $this->dateCreated = new \DateTimeImmutable();
        }
    }
}
