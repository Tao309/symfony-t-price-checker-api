<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\AccessTokenRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AccessTokenRepository::class)]
class AccessToken
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[ORM\Column(length: 255, unique: true)]
    public string $token;

    #[ORM\Column(length: 255)]
    public string $userIdentifier;

    #[ORM\Column]
    public \DateTimeImmutable $expiresAt;

    public function getId(): int
    {
        return $this->id;
    }

    public function isValid(): bool
    {
        return $this->expiresAt > new \DateTimeImmutable();
    }
}
