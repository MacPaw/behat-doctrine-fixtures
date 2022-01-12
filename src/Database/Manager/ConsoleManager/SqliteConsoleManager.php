<?php

declare(strict_types=1);

namespace BehatDoctrineFixtures\Database\Manager\ConsoleManager;

class SqliteConsoleManager
{
    public function changeMode(string $filename, int $permissions): void
    {
        chmod($filename, $permissions);
    }

    public function copy(string $from, string $to): void
    {
        copy($from, $to);
    }
}
