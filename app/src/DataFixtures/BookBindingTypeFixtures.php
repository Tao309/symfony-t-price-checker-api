<?php

namespace App\DataFixtures;

use App\Entity\BookBindingType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class BookBindingTypeFixtures extends Fixture
{
    private const array IMPORT_DATA = [
        [1, 'Твёрдый переплёт'],
        [2, 'Мягкий переплёт'],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::IMPORT_DATA as $data) {
            $model = new BookBindingType();
            $model->setId($data[0]);
            $model->setLabel($data[1]);

            $manager->persist($model);
        }

        $manager->flush();
    }
}
