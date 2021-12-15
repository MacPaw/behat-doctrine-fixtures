<?php

declare(strict_types=1);

namespace BehatDoctrineFixtures\Context;

use BehatDoctrineFixtures\Database\DatabaseHelper;
use Behat\Behat\Context\Context;
use InvalidArgumentException;

class DatabaseContext implements Context
{
    protected DatabaseHelper $databaseHelper;
    protected string $dataFixturesPath;

    public function __construct(
        DatabaseHelper $databaseHelper,
        string $dataFixturesPath,
    ) {
        $this->databaseHelper = $databaseHelper;
        $this->dataFixturesPath = $dataFixturesPath;
    }

    /**
     * I load fixtures.
     *
     * @param string $aliases
     *
     * @throws InvalidArgumentException
     *
     * @Given /^I load fixtures "([^\"]*)"$/
     */
    public function loadFixtures(string $aliases): void
    {
        $aliases = array_map('trim', explode(',', $aliases));
        $fixtures = [];

        foreach ($aliases as $alias) {
            $fixture = sprintf('%s/%s.yml', $this->dataFixturesPath, $alias);

            if (!is_file($fixture)) {
                throw new InvalidArgumentException(sprintf('The "%s" fixture not found.', $alias));
            }

            $fixtures[] = $fixture;
        }

        $this->databaseHelper->loadFixtures($fixtures);
    }
}
