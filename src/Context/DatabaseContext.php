<?php

declare(strict_types=1);

namespace BehatDoctrineFixtures\Context;

use BehatDoctrineFixtures\Database\DatabaseHelperCollection;
use Behat\Behat\Context\Context;
use InvalidArgumentException;

class DatabaseContext implements Context
{
    protected DatabaseHelperCollection $databaseHelperCollection;

    public function __construct(
        DatabaseHelperCollection $databaseHelperCollection
    ) {
        $this->databaseHelperCollection = $databaseHelperCollection;
    }

    /**
     * @Given I load fixtures :fixtures
     */
    public function loadFixturesForDefaultConnection(string $fixtures): void
    {
        $this->loadFixtures('default', $fixtures);
    }

    /**
     * @Given I load fixtures :fixtures for :connectionName connection
     */
    public function loadFixturesForGivenConnection(string $fixtures, string $connectionName): void
    {
        $this->loadFixtures($connectionName, $fixtures);
    }

    public function loadFixtures(string $connectionName, string $fixtures): void
    {
        $fixtureAliases = array_map('trim', explode(',', $fixtures));
        $this->databaseHelperCollection->getDatabaseHelperByConnectionName($connectionName)
            ->loadFixtures($fixtureAliases);
    }
}
