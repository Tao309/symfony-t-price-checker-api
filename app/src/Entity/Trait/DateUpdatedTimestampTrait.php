<?php

declare(strict_types=1);

namespace App\Entity\Trait;

use Doctrine\ORM\Mapping as ORM;

trait DateUpdatedTimestampTrait
{
    public function getDateUpdated(): ?\DateTime
    {
        return $this->dateUpdated;
    }

    public function setDateUpdated(\DateTime $dateUpdated): static
    {
        $this->dateUpdated = $dateUpdated;

        return $this;
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        if (null === $this->dateUpdated) {
            $this->dateUpdated = new \DateTime();
        }
    }

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        if (null === $this->dateUpdated) {
            $this->dateUpdated = new \DateTime();
        }
    }
}
