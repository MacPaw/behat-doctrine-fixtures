# Installation Guide

## Step 1: Install the Bundle
To begin, navigate to your project directory and use Composer to download the bundle.

### For applications using Symfony Flex:
Simply run the following command:

```bash
composer require --dev macpaw/behat-doctrine-fixtures
```

### For applications without Symfony Flex:
If your project doesn't use Symfony Flex, run the same command:

```bash
composer require --dev macpaw/behat-doctrine-fixtures
```

Make sure that Composer is installed globally on your machine. If not, refer to the [Composer installation guide](https://getcomposer.org/doc/00-intro.md) for assistance.

Next, you'll need to manually register the bundle in the `AppKernel.php` file. Add the following line to the `registerBundles` method:

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

Step 2: Create Behat Doctrine Fixtures Config:
----------------------------------
`config/packages/test/behat_doctrine_fixtures.yaml `

Configurating behat database context

```yaml
behat_doctrine_fixtures:
  connections:
    default:
      database_fixtures_paths:
        - <path to directory with your fixtures>
```


Step 3: Configure Behat
=============
Go to `behat.yml`

```yaml
...
  contexts:
    - BehatDoctrineFixtures\Context\DatabaseContext
...
```