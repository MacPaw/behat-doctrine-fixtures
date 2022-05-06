<?php

declare(strict_types=1);

namespace BehatDoctrineFixtures\Database\Exception;

use Exception;

class FixtureFileNotFound extends Exception
{
    /**
     * @var array<string>
     */
    private array $parameters;

    public function __construct(string $alias)
    {
        parent::__construct('fixtureFile.notFound');

        $this->parameters = [
            'alias' => $alias
        ];
    }

    /**
     * @return array<string>
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }
}
