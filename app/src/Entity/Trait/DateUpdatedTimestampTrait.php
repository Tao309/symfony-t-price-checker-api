<?php

namespace App\Entity\Trait;

use Doctrine\ORM\Mapping as ORM;

trait DateUpdatedTimestampTrait
{
    public function getDateUpdated(): ?\DateTimeImmutable
    {
        return $this->dateUpdated;
    }

    public function setDateUpdated(\DateTimeImmutable $dateUpdated): static
    {
        $this->dateUpdated = $dateUpdated;

        return $this;
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        if (null === $this->dateUpdated) {
            $this->dateUpdated = new \DateTimeImmutable();
        }
    }

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        if (null === $this->dateUpdated) {
            $this->dateUpdated = new \DateTimeImmutable();
        }
    }
}
