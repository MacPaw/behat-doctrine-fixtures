<?php

declare(strict_types=1);

namespace BehatDoctrineFixtures\Database\Manager\ConsoleManager;

class SqliteConsoleManager
{
    /**
     * @param mixed $params
     */
    public function changeMode(...$params): void
    {
        chmod(...$params);
    }

    /**
     * @param mixed $params
     */
    public function copy(...$params): void
    {
        copy(...$params);
    }
}
