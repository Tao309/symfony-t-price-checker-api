<?php

namespace App\Entity\Trait;

use Symfony\Component\Security\Core\User\UserInterface;

trait UserAwareTrait
{
    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    public function setUser(?UserInterface $user): static
    {
        $this->user = $user;

        return $this;
    }
}
