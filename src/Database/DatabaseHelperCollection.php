<?php

declare(strict_types=1);

namespace BehatDoctrineFixtures\Database;

use BehatDoctrineFixtures\Database\Exception\AlreadyExistDatabaseHelperCollectionException;
use BehatDoctrineFixtures\Database\Exception\NotFoundDatabaseHelperCollectionException;

class DatabaseHelperCollection
{
    private array $items = [];

    public function __construct(iterable $databaseHelperList)
    {
        foreach ($databaseHelperList as $databaseHelper) {
            $this->add($databaseHelper);
        }
    }

    public function add(DatabaseHelper $databaseHelper): void
    {
        $connectionName = $databaseHelper->getConnectionName();

        if (isset($this->items[$connectionName])) {
            throw new AlreadyExistDatabaseHelperCollectionException($connectionName);
        }

        $this->items[$connectionName] = $databaseHelper;
    }

    public function getDatabaseHelperByConnectionName(string $connectionName): DatabaseHelper
    {
        if (!isset($this->items[$connectionName])) {
            throw new NotFoundDatabaseHelperCollectionException($connectionName);
        }

        return $this->items[$connectionName];
    }
}
