Upgrading From 1.x To 2.0
=========================

In 2.0 we did major changes. The biggest is adding the support for multiple database connections. To help you migrate to 2.0, we created this guide.

Step 1: Update configuration file
---------------------------------------

* There is new option called ``connections`` under the ``behat_doctrine_fixtures``. It receives an array of connection names with necessary options.

* ``behat_doctrine_fixtures.database_context.dataFixturesPath`` was replaced with ``behat_doctrine_fixtures.connections.<connectionName>.database_fixtures_paths`` and now you can pass an array of paths here.

* ``behat_doctrine_fixtures.database_context`` options is now boolean and determines if DatabaseContext will be loaded, default value - ``true``.

Before:

  ```yml
  behat_doctrine_fixtures:
    database_context:
      dataFixturesPath: '%kernel.project_dir%/tests/DataFixtures/ORM'
  ```

After:

  ```yml
    behat_doctrine_fixtures:
      connections:
        default:
          database_fixtures_paths:
            - '%kernel.project_dir%/tests/DataFixtures/ORM'
  ```

Step 2: Update DatabaseHelper usage
---------------------------

If you were using ``DatabaseHelper`` to load fixtures outside the context, now you need to retrieve ``DatabaseHelperCollection`` from container:
```php
$databaseHelperCollection = $container->get(DatabaseHelperCollection::class);
```
and then get ``DatabaseHelper`` for your specific connection:
```php
$databaseHelper = $databaseHelperCollection->getDatabaseHelperByConnectionName($connectionName);
```

Step 3: Update the bundle
-------------------------

Change the constraint of ``macpaw/behat-doctrine-fixtures`` in your ``composer.json`` file
to ``^2.0``:

```json
{
    "require-dev": {
        "macpaw/behat-doctrine-fixtures": "^2.0"
    }
}
```

Then update your dependencies:

```
composer update
```

Step 4: Review the changes
--------------------------

It's almost finished!

As most of the changes were automated you should check that they did not break
anything. Run your test suite, review, do whatever you think is useful before
pushing the changes.

Then, commit the changes, push them, and enjoy!