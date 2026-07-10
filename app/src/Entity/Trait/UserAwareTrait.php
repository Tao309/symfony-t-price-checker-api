<?php

namespace App\Entity\Trait;

use Symfony\Component\Security\Core\User\UserInterface;

trait UserAwareTrait
{
    public function getUserCreated(): ?UserInterface
    {
        return $this->userCreated;
    }

    public function setUserCreated(?UserInterface $userCreated): static
    {
        $this->userCreated = $userCreated;

        return $this;
    }
}
