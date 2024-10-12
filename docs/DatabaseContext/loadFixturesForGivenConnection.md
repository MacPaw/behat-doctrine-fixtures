# Method Documentation: `loadFixturesForGivenConnection`

## Description

The `loadFixturesForGivenConnection` method is a Behat step definition that loads a specified list of fixtures into a given database connection. This step allows flexibility in choosing the database connection to load the fixtures into, which is useful in multi-database environments.

## Method Details

### Method Signature

```php
/**
 * @Given I load fixtures :fixtures for :connectionName connection
 */
public function loadFixturesForGivenConnection(string $fixtures, string $connectionName): void
```

### Use in tests

```gherkin
Given I load fixtures "UserFixture, ProductFixture" for "test_connection" connection
```