<?php

declare(strict_types=1);

namespace BehatDoctrineFixtures\Database\Manager;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\ORM\Tools\SchemaTool;

class SqliteDatabaseManager extends DatabaseManager
{
    /**
     * @param array<string> $fixtures
     */
    public function saveBackup(array $fixtures): void
    {
        $databasePath = $this->getDatabaseName();
        $backupFilename = $this->getBackupFilename($fixtures);

        copy($databasePath, $backupFilename);

        $this->log('Database backup saved', ['fixtures' => $fixtures]);
    }

    /**
     * @param array<string> $fixtures
     */
    public function loadBackup(array $fixtures): void
    {
        $backupFileName = $this->getBackupFilename($fixtures);
        $databasePath = $this->getDatabaseName();

        $this->entityManager->clear();
        $this->connection->close();

        copy($backupFileName, $databasePath);
        chmod($databasePath, 0666);

        $this->log('Database backup loaded');
    }

    public function prepareSchema(): void
    {
        if ($this->schemaCreated) {
            $this->loadBackup([]);
        }

        $this->createSchema();
        $this->saveBackup([]);
    }

    public function createSchema(): void
    {
        $schemaTool = new SchemaTool($this->entityManager);
        $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();

        $schemaTool->dropDatabase();
        #todo Think about how to get rid of this try
        try {
            $this->connection->executeStatement("DROP table v_product_plan");
        } catch (\Throwable $ex) {
        }

        $schema = $schemaTool->getSchemaFromMetadata($metadata);
        $this->adaptDatabaseSchemaToSqlite($schema);
        $createSchemaSql = $schema->toSql($this->connection->getDatabasePlatform());

        foreach ($createSchemaSql as $sql) {
            $this->connection->executeStatement($sql);
        }

        $this->schemaCreated = true;
        $this->log('Schema created');
    }

    private function adaptDatabaseSchemaToSqlite(Schema $schema): void
    {
        foreach ($schema->getTables() as $table) {
            foreach ($table->getColumns() as $column) {
                if ($column->hasCustomSchemaOption('collation')) {
                    $column->setCustomSchemaOptions(array_diff_key(
                        $column->getCustomSchemaOptions(),
                        ['collation' => true]
                    ));
                }
            }
        }
    }

    /**
     * @param array<string> $fixtures
     */
    protected function getBackupFilename(array $fixtures): string
    {
        $databaseName = $this->getDatabaseName();

        return sprintf('%s_%s.sql', $databaseName, md5(serialize($fixtures)));
    }

    protected function getDatabaseName(): string
    {
        return $this->connection->getParams()['path'];
    }
}
