<?php

namespace BehatDoctrineBundle\Database\Exception;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Exception;

class DatabaseManagerNotFoundForCurrentPlatform extends Exception
{
    public function __construct(AbstractPlatform $databasePlatform){
        parent::__construct('databaseManager.forPlatform.notFound', [
            'databasePlatform' => get_class($databasePlatform),
        ]);
    }
}
