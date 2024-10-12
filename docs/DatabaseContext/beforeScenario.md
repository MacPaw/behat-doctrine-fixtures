# Method Documentation: `beforeScenario`

## Description

The `beforeScenario` method is a hook that is executed before each scenario in Behat. It prepares the database by loading fixtures for all the configured database connections. This method is annotated with `@BeforeScenario`, which ensures it is triggered automatically by Behat before any scenario runs.

## Method Details

### Method Signature

```php
/**
 * @BeforeScenario
 */
public function beforeScenario(): void
