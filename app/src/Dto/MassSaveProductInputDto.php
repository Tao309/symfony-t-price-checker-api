<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class MassSaveProductInputDto
{
    /**
     * @var array<array>
     */
    #[Assert\NotBlank]
    #[Assert\Type('array')]
    public array $products = [];
}
