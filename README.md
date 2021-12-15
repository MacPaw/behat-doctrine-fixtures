Behat Doctrine Fixtures
=================================

Installation
============

Step 1: Install Bundle
----------------------------------
Open a command console, enter your project directory and execute:

```console
$ composer require --dev macpaw/behat-doctrine-fixtures
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

Step 2: Enable the Bundle
----------------------------------
Then, enable the bundle by adding it to the list of registered bundles
in the `app/AppKernel.php` file of your project:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...
            BehatDoctrineFixtures\BehatDoctrineFixturesBundle::class => ['test' => true]
        );

        // ...
    }

    // ...
}
```

Step 3: Create Behat Doctrine Fixtures Config:
----------------------------------
`config/packages/test/behat_doctrine_fixtures.yaml `

Configurating behat database context

```yaml
behat_doctrine_fixtures:
    database_context:
        dataFixturesPath: <path to directory with your fixtures>
```
