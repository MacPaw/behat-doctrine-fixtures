<?php

declare(strict_types=1);

namespace BehatDoctrineBundle\Database;

use Doctrine\ORM\EntityManagerInterface;
use Fidry\AliceDataFixtures\Bridge\Doctrine\Persister\ObjectManagerPersister;
use Fidry\AliceDataFixtures\Loader\PersisterLoader;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use BehatDoctrineBundle\Database\Manager\DatabaseManager;

class DatabaseHelper
{
    private EntityManagerInterface $entityManager;
    private PersisterLoader $fixturesLoader;
    private LoggerInterface $logger;
    private string $cacheDir;
    private ?DatabaseManager $databaseManager = null;

    public function __construct(
        EntityManagerInterface $entityManager,
        PersisterLoader $fixturesLoader,
        LoggerInterface $logger,
        string $cacheDir
    ) {
        $this->entityManager = $entityManager;
        $this->fixturesLoader = $fixturesLoader->withPersister(new ObjectManagerPersister($entityManager));
        $this->logger = $logger;
        $this->cacheDir = $cacheDir;
    }

    public function loadFixtures(array $fixtures = []): void
    {
        if ($cacheDriver = $this->entityManager->getMetadataFactory()->getCacheDriver()) {
            $cacheDriver->deleteAll();
        }

        $databaseManager = $this->getDatabaseManager();

        if($databaseManager->backupExists($fixtures)) {
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
            : DatabaseManagerFactory::createDatabaseManager($this->entityManager, $this->logger, $this->cacheDir);
    }
}
