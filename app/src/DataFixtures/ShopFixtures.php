<?php

namespace App\DataFixtures;

use App\Entity\Shop;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ShopFixtures extends Fixture
{
    private const array IMPORT_DATA = [
        [1, 'ozon', 'ozon.ru'],
        [2, 'wildberries', 'wildberries.ru'],
        [3, 'chitai-gorod', 'chitai-gorod.ru'],
        [4, 'knigofan', 'knigofan.ru'],
        [5, 'ffan', 'ffan.ru'],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::IMPORT_DATA as $data) {
            $model = new Shop();
            $model->setId($data[0]);
            $model->setType($data[1]);
            $model->setDomain($data[2]);

            $manager->persist($model);
        }

        $manager->flush();
    }
}
