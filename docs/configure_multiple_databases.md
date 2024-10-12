
# Using Multiple Database Connections

To work with multiple databases in your Behat project, you can define additional connections under `behat_doctrine_fixtures.connections`. Each connection can have its own configuration, migrations, and fixtures. This setup allows you to run tests on different databases within the same project.

## Configuration Example

Below is an example configuration that demonstrates how to set up two database connections:

```yaml
behat_doctrine_fixtures:
  connections:
    default:
      database_fixtures_paths:
        - path/to/fixtures/default
      run_migrations_command: doctrine:migrations:migrate --connection=default
    second_connection:
      database_fixtures_paths:
        - path/to/fixtures/second
      run_migrations_command: doctrine:migrations:migrate --connection=second
```

In this configuration:

- **default**: The default database connection.
    - `database_fixtures_paths`: Points to the directory containing fixtures for this connection.
    - `run_migrations_command`: The custom command to run migrations for this connection.
- **second_connection**: A secondary database connection.
    - `database_fixtures_paths`: Points to a different set of fixtures.
    - `run_migrations_command`: A custom migration command for the second connection.

## Usage Example in Tests

To use these multiple connections in your Behat tests, you may want to load fixtures or perform database operations on different databases depending on the context. Here’s an example Behat step definition:

```php
/**
 * @Given I load fixtures :fixtures for :connectionName connection
 */
public function loadFixturesForGivenConnection(string $fixtures, string $connectionName): void
{
    $fixtureAliases = array_map('trim', explode(',', $fixtures));
    $this->loadFixtures($connectionName, $fixtureAliases);
}
```

You can then use this step in your Behat scenario:

```gherkin
Scenario: Load fixtures for multiple databases
  Given I load fixtures "Base, User" for "default" connection
  And I load fixtures "Order" for "second_connection" connection
```

## How it Works

- **Multiple Connections**: Each connection (e.g., `default`, `second_connection`) can have its own set of fixtures and migrations.
- **Fixture Loading**: Behat allows you to specify which connection to load fixtures for, enabling testing against multiple databases within the same test suite.
- **Custom Migrations**: The `run_migrations_command` ensures that the correct migrations are applied to each respective database before the tests run.

This setup is useful when your application interacts with more than one database, such as in microservices architectures or when testing different database configurations.
