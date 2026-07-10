<?php

namespace App\Entity;

use Symfony\Component\Security\Core\User\UserInterface;

interface UserAwareInterface
{
    public function setUserCreated(?UserInterface $user): static;
}
