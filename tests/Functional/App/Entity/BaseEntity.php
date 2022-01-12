<?php

declare(strict_types=1);

namespace BehatDoctrineFixtures\Tests\Functional\App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 *
 * @ORM\Table(name="base_entity")
 */
class BaseEntity
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     */
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
