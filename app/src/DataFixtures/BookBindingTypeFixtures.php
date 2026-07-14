<?php

namespace App\DataFixtures;

use App\Entity\BookBindingType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;

class BookBindingTypeFixtures extends Fixture
{
    private const array IMPORT_DATA = [
        [1, 'Твёрдый переплёт'],
        [2, 'Мягкий переплёт'],
    ];

    public function __construct(
        protected readonly EntityManagerInterface $em,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $metadata = $this->em->getClassMetaData(BookBindingType::class);
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
        $metadata->setIdGenerator(new \Doctrine\ORM\Id\AssignedGenerator());

        foreach (self::IMPORT_DATA as $data) {
            $model = new BookBindingType();
            $model->setId($data[0]);
            $model->setLabel($data[1]);

            $manager->persist($model);
        }

        $manager->flush();
    }
}
