<?php

namespace App\DataFixtures;

use App\Entity\City;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CityFixtures extends Fixture
{
    private const array IMPORT_DATA = [
        [1, 'КРР', 'Краснодар'],
        [2, 'МОВ', 'Москва'],
        [3, 'СПТ', 'Санкт-Петербург'],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::IMPORT_DATA as $data) {
            $model = new City();
            $model->setId($data[0]);
            $model->setCode($data[1]);
            $model->setTitle($data[2]);

            $manager->persist($model);
        }

        $manager->flush();
    }
}
