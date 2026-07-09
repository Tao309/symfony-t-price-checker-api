<?php

namespace App\Entity;

use Symfony\Component\Security\Core\User\UserInterface;

interface UserAwareInterface
{
    public function setUser(?UserInterface $user): static;
}
