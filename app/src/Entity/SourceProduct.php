<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model;
use ApiPlatform\OpenApi\Model\Operation;
use App\Controller\LinkSourceProductController;
use App\Controller\UnlinkSourceProductController;
use App\Entity\Trait\DateCreatedTimestampTrait;
use App\Entity\Trait\DateUpdatedTimestampTrait;
use App\Entity\Trait\IdentifierTrait;
use App\Repository\SourceProductRepository;
use App\State\SourceProductsSearchProvider;
use App\State\WrapEntityProcessor;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\MaxDepth;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SourceProductRepository::class)]
#[ORM\Table(options: ['comment' => 'Источник товара'])]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new Get(
            requirements: ['id' => '\d+'],
            openapi: new Operation(
                summary: 'Получить источник товара',
            ),
            normalizationContext: ['groups' => [Product::GROUP_READ]],
        ),

        new GetCollection(
            uriTemplate: '/source_products/search/{title}',
            uriVariables: ['title'],
            defaults: ['title' => ''],
            requirements: ['title' => '.{3,}+'],
            openapi: new Operation(
                summary: 'Найти источники товаров по названию',
                parameters: [
                    new Model\Parameter(
                        name: 'title',
                        in: 'path',
                        required: true,
                        schema: [
                            'type' => 'string',
                        ]
                    ),
                ]
            ),
            normalizationContext: ['groups' => [self::GROUP_SOURCE_PRODUCT_READ]],
            provider: SourceProductsSearchProvider::class,
        ),
        new Post(
            formats: ['json' => ['application/json']],
            openapi: new Operation(
                responses: [
                    200 => new Model\Response(
                        description: 'Успешный ответ',
                        content: new \ArrayObject([
                            'application/ld+json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'entity' => [
                                            '$ref' => '#/components/schemas/SourceProduct.jsonld',
                                        ],
                                    ],
                                ],
                            ],
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'entity' => [
                                            '$ref' => '#/components/schemas/SourceProduct',
                                        ],
                                    ],
                                ],
                            ],
                        ])
                    ),
                ],
                summary: 'Создать источник товара',
            ),
            denormalizationContext: ['groups' => [self::GROUP_SOURCE_PRODUCT_WRITE]],
            processor: WrapEntityProcessor::class,
        ),
        new Patch(
            inputFormats: ['json' => ['application/json']],
            requirements: ['id' => '\d+'],
            openapi: new Operation(
                responses: [
                    200 => new Model\Response(
                        description: 'Успешный ответ',
                        content: new \ArrayObject([
                            'application/ld+json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'entity' => [
                                            '$ref' => '#/components/schemas/SourceProduct.jsonld',
                                        ],
                                    ],
                                ],
                            ],
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'entity' => [
                                            '$ref' => '#/components/schemas/SourceProduct',
                                        ],
                                    ],
                                ],
                            ],
                        ])
                    ),
                ],
                summary: 'Обновить источник товара',
            ),
            denormalizationContext: ['groups' => [self::GROUP_SOURCE_PRODUCT_WRITE]],
            processor: WrapEntityProcessor::class,
        ),
        new Post(
            uriTemplate: '/source_products/link/{productId}/{sourceProductId}',
            uriVariables: [],
            controller: LinkSourceProductController::class,
            openapi: new Operation(
                summary: 'Привязать источник товара к продукту',
                parameters: [
                    new Model\Parameter(
                        name: 'productId',
                        in: 'path',
                        required: true,
                        schema: [
                            'type' => 'integer',
                        ]
                    ),
                    new Model\Parameter(
                        name: 'sourceProductId',
                        in: 'path',
                        required: true,
                        schema: [
                            'type' => 'integer',
                        ]
                    ),
                ]
            ),
            read: false,
            name: 'link_source_product',
        ),
        new Post(
            uriTemplate: '/source_products/unlink/{productId}',
            uriVariables: [],
            controller: UnlinkSourceProductController::class,
            openapi: new Operation(
                summary: 'Отвязать источник товара от продукта',
                parameters: [
                    new Model\Parameter(
                        name: 'productId',
                        in: 'path',
                        required: true,
                        schema: [
                            'type' => 'integer',
                        ]
                    ),
                ]
            ),
            read: false,
            name: 'unlink_source_product',
        ),
    ],
    order: ['id' => 'DESC'],
    security: "is_granted('ROLE_USER')"
)]
class SourceProduct
{
    use DateCreatedTimestampTrait;
    use DateUpdatedTimestampTrait;
    use IdentifierTrait;

    public const string GROUP_SOURCE_PRODUCT_READ = 'source_product:read';
    public const string GROUP_SOURCE_PRODUCT_WRITE = 'source_product:write';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([Product::GROUP_READ, self::GROUP_SOURCE_PRODUCT_READ])]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([Product::GROUP_READ, self::GROUP_SOURCE_PRODUCT_READ, self::GROUP_SOURCE_PRODUCT_WRITE])]
    private ?SourceProductType $sourceProductType = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotNull(groups: [self::GROUP_SOURCE_PRODUCT_WRITE])]
    #[Assert\Length(min: 5, max: 255, groups: [self::GROUP_SOURCE_PRODUCT_WRITE])]
    #[Groups([Product::GROUP_READ, self::GROUP_SOURCE_PRODUCT_READ, self::GROUP_SOURCE_PRODUCT_WRITE])]
    private ?string $title = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([Product::GROUP_READ, self::GROUP_SOURCE_PRODUCT_READ])]
    private ?User $userCreated = null;

    #[ORM\Column(type: Types::DATETIMETZ_MUTABLE)]
    #[Groups([Product::GROUP_READ, self::GROUP_SOURCE_PRODUCT_READ])]
    private ?\DateTime $dateUpdated = null;

    #[ORM\Column(type: Types::DATETIMETZ_MUTABLE)]
    #[Groups([Product::GROUP_READ, self::GROUP_SOURCE_PRODUCT_READ])]
    private ?\DateTime $dateCreated = null;

    #[ORM\OneToOne(targetEntity: SourceProductUserData::class, mappedBy: 'sourceProduct')]
    #[MaxDepth(1)]
    #[Groups([Product::GROUP_READ, self::GROUP_SOURCE_PRODUCT_READ])]
    private ?SourceProductUserData $sourceProductUserData = null;

    public function getSourceProductType(): ?SourceProductType
    {
        return $this->sourceProductType;
    }

    public function setSourceProductType(?SourceProductType $sourceProductType): static
    {
        $this->sourceProductType = $sourceProductType;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getUserCreated(): ?User
    {
        return $this->userCreated;
    }

    public function setUserCreated(?User $userCreated): static
    {
        $this->userCreated = $userCreated;

        return $this;
    }

    public function getSourceProductUserData(): ?SourceProductUserData
    {
        return $this->sourceProductUserData;
    }

    public function setSourceProductUserData(SourceProductUserData $sourceProductUserData): static
    {
        $this->sourceProductUserData = $sourceProductUserData;

        return $this;
    }
}
