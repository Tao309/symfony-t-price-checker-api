<?php

declare(strict_types=1);

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManagerInterface;

abstract class CommonFixture extends Fixture
{
    protected ?string $seqTable = null;

    public function __construct(
        protected readonly EntityManagerInterface $em,
    ) {
    }

    protected function updateSequence(): void
    {
        if ($this->em->getConnection()->getParams()['driver'] === 'pdo_mysql') {
            return;
        }

        if (!$this->seqTable) {
            return;
        }

        $this->seqTable = $this->seqTable === 'user' ? '"' . $this->seqTable . '"' : $this->seqTable;

        $connection = $this->em->getConnection();

        $lastId = $connection->fetchOne('SELECT MAX(id) FROM ' . $this->seqTable);

        if (empty($lastId)) {
            throw new \RuntimeException(
                \sprintf('Не найдено значение last ID для %s', $this->seqTable)
            );
        }

        $connection->executeStatement("SELECT setval(pg_get_serial_sequence('" . $this->seqTable . "', 'id'), COALESCE(MAX(id), 0) + 1, false) FROM " . $this->seqTable . ';');
    }
}
