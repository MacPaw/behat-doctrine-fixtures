<?php

declare(strict_types=1);

namespace BehatDoctrineFixtures\Database\Exception;

use Exception;

class NotFoundDatabaseHelperCollectionException extends Exception
{
    /**
     * @var array<string>
     */
    private array $parameters;

    public function __construct(string $connectionName)
    {
        parent::__construct('databaseHelperCollection.forConnection.notFound');

        $this->parameters = [
            'connectionName' => $connectionName
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
