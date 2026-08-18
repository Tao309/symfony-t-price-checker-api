<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class ArchiveProductInputDto
{
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[SerializedName('shop_product_id')]
    public string $shopProductId;

    #[Assert\Choice(choices: [true, false])]
    #[Assert\Type('bool')]
    public bool $value;
}
