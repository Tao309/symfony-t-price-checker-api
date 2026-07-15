<?php

declare(strict_types=1);

namespace App\Entity;

use Symfony\Component\Security\Core\User\UserInterface;

interface UserAwareInterface
{
    public function setUserCreated(?UserInterface $user): static;
}
