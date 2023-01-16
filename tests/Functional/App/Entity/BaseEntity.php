<?php

declare(strict_types=1);

namespace BehatDoctrineFixtures\Tests\Functional\App\Entity;

class BaseEntity
{
    private int $id;

    public function __construct(
        int $id
    ) {
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }
}
