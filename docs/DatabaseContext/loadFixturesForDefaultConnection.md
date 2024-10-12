# Method Documentation: `loadFixturesForDefaultConnection`

## Description

The `loadFixturesForDefaultConnection` method is a Behat step definition that loads a specified list of fixtures into the default database connection. This step can be used in Behat scenarios to load predefined data into the database before running tests.

## Method Details

### Method Signature

```php
/**
 * @Given I load fixtures :fixtures
 */
public function loadFixturesForDefaultConnection(string $fixtures): void
```

### Use in tests

```gherkin
Given I load fixtures "UserFixture, ProductFixture"
```
