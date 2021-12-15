<?php

declare(strict_types=1);

namespace BehatDoctrineFixtures\Database\Exception;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Exception;

class DatabaseManagerNotFoundForCurrentPlatform extends Exception
{
    /**
     * @var array<string>
     */
    private array $parameters;

    public function __construct(AbstractPlatform $databasePlatform)
    {
        parent::__construct('databaseManager.forPlatform.notFound');

        $this->parameters = [
            'databasePlatform' => get_class($databasePlatform),
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
