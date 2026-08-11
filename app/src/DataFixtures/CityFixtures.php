<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\City;
use Doctrine\Persistence\ObjectManager;

class CityFixtures extends CommonFixture
{
    protected ?string $seqTable = 'city';

    private const array IMPORT_DATA = [
        [1, 'КРР', 'Краснодар'],
        [2, 'МОВ', 'Москва'],
        [3, 'СПТ', 'Санкт-Петербург'],
    ];

    public function load(ObjectManager $manager): void
    {
        $metadata = $this->em->getClassMetaData(City::class);
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
        $metadata->setIdGenerator(new \Doctrine\ORM\Id\AssignedGenerator());

        foreach (self::IMPORT_DATA as $data) {
            $model = new City();
            $model->setId($data[0]);
            $model->setCode($data[1]);
            $model->setTitle($data[2]);

            $manager->persist($model);
        }

        $manager->flush();
        $this->updateSequence();
    }
}
