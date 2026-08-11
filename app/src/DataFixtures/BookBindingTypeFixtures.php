<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\BookBindingType;
use Doctrine\Persistence\ObjectManager;

class BookBindingTypeFixtures extends CommonFixture
{
    protected ?string $seqTable = 'book_binding_type';

    private const array IMPORT_DATA = [
        [1, 'Твёрдый переплёт'],
        [2, 'Мягкий переплёт'],
    ];

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
        $this->updateSequence();
    }
}
