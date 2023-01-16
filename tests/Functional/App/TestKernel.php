<?php

namespace BehatDoctrineFixtures\Tests\Functional\App;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class TestKernel extends Kernel
{
    public function __construct()
    {
        parent::__construct('test', false);
    }

    public function registerBundles(): iterable
    {
        $bundles = [
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new \Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),
            new \Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),
            new \Fidry\AliceDataFixtures\Bridge\Symfony\FidryAliceDataFixturesBundle(),
            new \Nelmio\Alice\Bridge\Symfony\NelmioAliceBundle(),
            new \BehatDoctrineFixtures\BehatDoctrineFixturesBundle(),
        ];

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/config/doctrine.yml');
        $loader->load(__DIR__ . '/config/doctrine_migrations.yml');
        $loader->load(__DIR__ . '/config/behat_doctrine_fixtures.yml');
    }

    public function getCacheDir(): string
    {
        return __DIR__.'/var/cache';
    }

    public function getProjectDir(): string
    {
        return __DIR__;
    }
}