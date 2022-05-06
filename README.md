Behat Doctrine Fixtures
=================================

| Version | Build Status | Code Coverage |
|:---------:|:-------------:|:-----:|
| `master`| [![CI][master Build Status Image]][master Build Status] | [![Coverage Status][master Code Coverage Image]][master Code Coverage] |
| `develop`| [![CI][develop Build Status Image]][develop Build Status] | [![Coverage Status][develop Code Coverage Image]][develop Code Coverage] |

## Migrate from 1.x to 2.0

[To migrate from 1.x to 2.0, follow our guide.](https://github.com/MacPaw/behat-doctrine-fixtures/blob/master/UPGRADE-2.0.md)

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

If you use PostgreSQL database, you also need to install postgresql-client:

```console
$ apt-get install -y postgresql-client
```

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
  connections:
    default:
      database_fixtures_paths:
        - <path to directory with your fixtures>
```

You don't need to explicitly pass database url here, since this bundle uses doctrine connection under the hood. For now, we support PostgreSQL and Sqlite databases.

If you want to use multiple databases in your project, just add one more connection under ``behat_doctrine_fixtures.connections`` with its own configuration:

```yaml
behat_doctrine_fixtures:
  connections:
    default:
      database_fixtures_paths:
        - <path to directory with your fixtures>
    <secondConnectionName>:
      run_migrations_command: <customMigrationsCommand>
      database_fixtures_paths:
        - <path to directory with your fixtures>
```

Step 4: Configure Behat
=============
Go to `behat.yml`

```yaml
...
  contexts:
    - BehatDoctrineFixtures\Context\DatabaseContext
...
```

[master Build Status]: https://github.com/macpaw/behat-doctrine-fixtures/actions?query=workflow%3ACI+branch%3Amaster
[master Build Status Image]: https://github.com/macpaw/behat-doctrine-fixtures/workflows/CI/badge.svg?branch=master
[develop Build Status]: https://github.com/macpaw/behat-doctrine-fixtures/actions?query=workflow%3ACI+branch%3Adevelop
[develop Build Status Image]: https://github.com/macpaw/behat-doctrine-fixtures/workflows/CI/badge.svg?branch=develop
[master Code Coverage]: https://codecov.io/gh/macpaw/behat-doctrine-fixtures/branch/master
[master Code Coverage Image]: https://img.shields.io/codecov/c/github/macpaw/behat-doctrine-fixtures/master?logo=codecov
[develop Code Coverage]: https://codecov.io/gh/macpaw/behat-doctrine-fixtures/branch/develop
[develop Code Coverage Image]: https://img.shields.io/codecov/c/github/macpaw/behat-doctrine-fixtures/develop?logo=codecov
