<?php

declare(strict_types=1);

namespace BehatDoctrineFixtures\Database;

use Doctrine\ORM\EntityManagerInterface;
use Fidry\AliceDataFixtures\Bridge\Doctrine\Persister\ObjectManagerPersister;
use Fidry\AliceDataFixtures\Loader\PersisterLoader;
use InvalidArgumentException;
use BehatDoctrineFixtures\Database\Manager\DatabaseManager;

class DatabaseHelper
{
    private EntityManagerInterface $entityManager;
    private PersisterLoader $fixturesLoader;
    private DatabaseManagerFactory $databaseManagerFactory;
    private ?DatabaseManager $databaseManager = null;

    public function __construct(
        DatabaseManagerFactory $databaseManagerFactory,
        EntityManagerInterface $entityManager,
        PersisterLoader $fixturesLoader
    ) {
        $this->databaseManagerFactory = $databaseManagerFactory;
        $this->entityManager = $entityManager;
        $this->fixturesLoader = $fixturesLoader->withPersister(new ObjectManagerPersister($entityManager));
    }

    /**
     * @param array<string> $fixtures
     */
    public function loadFixtures(array $fixtures = []): void
    {
        $databaseManager = $this->getDatabaseManager();

        if ($databaseManager->backupExists($fixtures)) {
            $databaseManager->loadBackup($fixtures);
            return;
        }

        $databaseManager->prepareSchema();

        if (!empty($fixtures)) {
            $fixturesObjects = $this->fixturesLoader->load($fixtures);

            if (count($fixturesObjects) === 0) {
                throw new InvalidArgumentException(sprintf('Fixtures were not loaded: %s', implode(', ', $fixtures)));
            }

            $databaseManager->saveBackup($fixtures);
        }

        $this->entityManager->clear();
    }

    private function getDatabaseManager(): DatabaseManager
    {
        return $this->databaseManager !== null
            ? $this->databaseManager
            : $this->databaseManagerFactory->createDatabaseManager($this->entityManager);
    }
}
