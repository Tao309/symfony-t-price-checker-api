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
    public function setDateUpdatedValue(): void
    {
        if ($this->dateUpdated === null) {
            $this->dateUpdated = new \DateTimeImmutable();
        }
    }
}
