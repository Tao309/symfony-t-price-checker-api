<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\OpenApi\Model\Operation;
use App\Entity\Trait\IdentifierTrait;
use App\Repository\BookBindingTypeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: BookBindingTypeRepository::class)]
#[ORM\Table(options: ['comment' => 'Тип переплёта'])]
#[ApiResource(
    operations: [
        new Get(
            requirements: ['id' => '\d+'],
            openapi: new Operation(
                summary: 'Получить тип переплёта',
            ),
            normalizationContext: ['groups' => [Book::GROUP_BOOK_READ]],
        ),
    ],
    order: ['id' => 'DESC'],
    security: "is_granted('ROLE_USER')"
)]
class BookBindingType
{
    use IdentifierTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([Book::GROUP_BOOK_READ])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups([Book::GROUP_BOOK_READ, Book::GROUP_BOOK_WRITE])]
    private ?string $label = null;

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }
}
