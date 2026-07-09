<?php

namespace App\Entity;

use App\Repository\AccessTokenRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AccessTokenRepository::class)]
class AccessToken
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id { get => $this->id; }

    #[ORM\Column(length: 255, unique: true)]
    public string $token;

    #[ORM\Column(length: 255)]
    public string $userIdentifier;

    #[ORM\Column]
    public \DateTimeImmutable $expiresAt;

    public bool $isValid {
        get => $this->expiresAt > new \DateTimeImmutable();
    }
}
