<?php

namespace App\EventListener;

use App\Entity\UserAwareInterface;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Events;
use Symfony\Bundle\SecurityBundle\Security;

#[AsEntityListener(event: Events::prePersist, method: 'prePersist')]
class UserAwareListener
{
    public function __construct(private Security $security) {}

    public function prePersist(UserAwareInterface $entity, PrePersistEventArgs $event): void
    {
        $user = $this->security->getUser();

        if ($user !== null) {
            $entity->setUser($user);
        }
    }
}
