<?php

declare(strict_types=1);

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
        if (null === $this->dateCreated) {
            $this->dateCreated = new \DateTimeImmutable();
        }
    }
}
