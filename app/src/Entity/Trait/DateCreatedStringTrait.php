<?php

declare(strict_types=1);

namespace App\Entity\Trait;

use Doctrine\ORM\Mapping as ORM;

trait DateCreatedStringTrait
{
    public function getDateCreatedString(): ?string
    {
        return $this->dateCreatedString;
    }

    public function setDateCreatedString(string $dateCreatedString): static
    {
        $this->dateCreatedString = $dateCreatedString;

        return $this;
    }

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        if (null === $this->dateCreatedString) {
            $this->dateCreatedString = new \DateTimeImmutable()->format('Y-m-d H:i:s');
        }
    }
}
