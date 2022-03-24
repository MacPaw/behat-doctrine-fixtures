<?php

declare(strict_types=1);

namespace BehatDoctrineFixtures\Context;

use BehatDoctrineFixtures\Database\DatabaseHelperCollection;
use Behat\Behat\Context\Context;

class DatabaseContext implements Context
{
    protected DatabaseHelperCollection $databaseHelperCollection;

    public function __construct(
        DatabaseHelperCollection $databaseHelperCollection
    ) {
        $this->databaseHelperCollection = $databaseHelperCollection;
    }

    /**
     * @BeforeScenario
     */
    public function beforeScenario(): void
    {
        $connectionNameList = $this->databaseHelperCollection->getConnectionNameList();

        foreach ($connectionNameList as $connectionName) {
            $this->loadFixtures($connectionName, []);
        }
    }

    /**
     * @Given I load fixtures :fixtures
     */
    public function loadFixturesForDefaultConnection(string $fixtures): void
    {
        $fixtureAliases = array_map('trim', explode(',', $fixtures));
        $this->loadFixtures('default', $fixtureAliases);
    }

    /**
     * @Given I load fixtures :fixtures for :connectionName connection
     */
    public function loadFixturesForGivenConnection(string $fixtures, string $connectionName): void
    {
        $fixtureAliases = array_map('trim', explode(',', $fixtures));
        $this->loadFixtures($connectionName, $fixtureAliases);
    }

    public function loadFixtures(string $connectionName, array $fixtureAliases): void
    {
        $databaseHelper = $this->databaseHelperCollection->getDatabaseHelperByConnectionName($connectionName);
        $databaseHelper->loadFixtures($fixtureAliases);
    }
}
