<?php

declare(strict_types=1);

namespace BehatDoctrineBundle\Database\Manager;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;

class PgSqlDatabaseManager extends DatabaseManager
{
    public function saveBackup(array $fixtures): void
    {
        $backupFilename = $this->getBackupFilename($fixtures);

        if (file_exists($backupFilename)) {
            return;
        }

        $databaseName = $this->getDatabaseName();
        $password = "PGPASSWORD='{$this->connection->getParams()['password']}'";
        $user = $this->connection->getParams()['user'];
        $host = $this->connection->getParams()['host'];
        $port = $this->connection->getParams()['port'];
        # Needed for optimization
        $additionalParams = "--exclude-table=migration_versions --no-comments --disable-triggers --data-only";
        $appendStderrFile = "2> {$this->cacheDir}/pg_dump_log.txt";

        exec("{$password} pg_dump -U{$user} -h{$host} -p{$port} {$additionalParams} {$databaseName} > {$backupFilename} {$appendStderrFile}");

        $this->log('Database backup saved', ['fixtures' => $fixtures]);
    }

    public function loadBackup(array $fixtures): void
    {
        $this->dropData();

        if(empty($fixtures)){
            return;
        }

        $backupFilename = $this->getBackupFilename($fixtures);
        $databaseName = $this->getDatabaseName();
        $password = "PGPASSWORD='{$this->connection->getParams()['password']}'";
        $user = $this->connection->getParams()['user'];
        $host = $this->connection->getParams()['host'];
        $port = $this->connection->getParams()['port'];

        exec("{$password} psql -U{$user} -h{$host} -p{$port} {$databaseName} < {$backupFilename}");

        $this->log('Database backup loaded');
    }

    public function prepareSchema(): void
    {
        if(!$this->schemaCreated){
            $this->createSchema();
        }

        $this->dropData();
    }

    public function createSchema(): void
    {
        $this->createDatabase();

        exec('bin/console d:mi:mi --no-interaction');

        $this->schemaCreated = true;
        $this->log('Schema created');
    }

    private function dropData(): void
    {
        $purger = new ORMPurger($this->entityManager);
        $executor =  new ORMExecutor($this->entityManager, $purger);

        $executor->purge();
        $this->restartSequences();
    }

    private function restartSequences() {
        $sequences = $this->connection->executeQuery('SELECT * FROM information_schema.sequences')->fetchAllAssociative();
        foreach ($sequences as $sequence){
            $this->connection->executeStatement(sprintf('ALTER SEQUENCE %s RESTART WITH 1', $sequence['sequence_name']));
        }
    }

    protected function getBackupFilename(array $fixtures): string
    {
        $databaseName = $this->getDatabaseName();

        return sprintf('%s/%s_%s.sql', $this->cacheDir, $databaseName, md5(serialize($fixtures)));
    }

    function createDatabase(): void
    {
        exec('bin/console d:d:create');

        $this->log('Database created');
    }

    protected function getDatabaseName(): string
    {
        return $this->connection->getParams()['dbname'];
    }
}
