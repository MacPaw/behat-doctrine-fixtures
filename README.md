# Behat Doctrine Fixtures

| Version | Build Status | Code Coverage |
|:---------:|:-------------:|:-----:|
| `master`| [![CI][master Build Status Image]][master Build Status] | [![Coverage Status][master Code Coverage Image]][master Code Coverage] |
| `develop`| [![CI][develop Build Status Image]][develop Build Status] | [![Coverage Status][develop Code Coverage Image]][develop Code Coverage] |

## Installation
To install the Behat Doctrine Fixtures and integrate it with your Behat setup, follow the instructions provided in the [Installation Guide](docs/install.md).

## Database Support
This library allows you to easily work with fixtures for testing in Behat using Doctrine. It supports two types of databases:

* [How to configure SQLite](docs/configure_sqlite.md)  – a lightweight and fast database for testing.
* [How to configure PostgreSQL](docs/configure_postgre.md) – a powerful and popular relational database for production-like environments.
* [How multiple databases](docs/configure_multiple_databases.md) - To configure multiple databases, define separate connection settings for each database in your Doctrine configuration and reference them appropriately in your application.

## How Usage in Behat
These methods can be used in Behat scenarios to load fixtures into the database, preparing test data for scenarios. For more detailed information on each method, refer to the links above.

### Methods

#### [beforeScenario](docs/DatabaseContext/beforeScenario.md)
The `beforeScenario` method loads fixtures for all configured database connections before each Behat scenario. This ensures that the database is in a clean state before testing.

#### [loadFixturesForDefaultConnection](docs/DatabaseContext/loadFixturesForDefaultConnection.md)
The `loadFixturesForDefaultConnection` method loads fixtures into the default database connection. It accepts a comma-separated list of fixture names and loads them into the default connection.

#### [loadFixturesForGivenConnection](docs/DatabaseContext/loadFixturesForGivenConnection.md)
The `loadFixturesForGivenConnection` method loads fixtures into a specified database connection. This method allows flexibility by letting you choose which database connection the fixtures should be loaded into, which is useful in multi-database environments.

## Migrate from 1.x to 2.0

[To migrate from 1.x to 2.0, follow our guide.](https://github.com/MacPaw/behat-doctrine-fixtures/blob/master/UPGRADE-2.0.md)

[master Build Status]: https://github.com/macpaw/behat-doctrine-fixtures/actions?query=workflow%3ACI+branch%3Amaster
[master Build Status Image]: https://github.com/macpaw/behat-doctrine-fixtures/workflows/CI/badge.svg?branch=master
[develop Build Status]: https://github.com/macpaw/behat-doctrine-fixtures/actions?query=workflow%3ACI+branch%3Adevelop
[develop Build Status Image]: https://github.com/macpaw/behat-doctrine-fixtures/workflows/CI/badge.svg?branch=develop
[master Code Coverage]: https://codecov.io/gh/macpaw/behat-doctrine-fixtures/branch/master
[master Code Coverage Image]: https://img.shields.io/codecov/c/github/macpaw/behat-doctrine-fixtures/master?logo=codecov
[develop Code Coverage]: https://codecov.io/gh/macpaw/behat-doctrine-fixtures/branch/develop
[develop Code Coverage Image]: https://img.shields.io/codecov/c/github/macpaw/behat-doctrine-fixtures/develop?logo=codecov
