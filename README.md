# Test Group Separator

## Description:
It is possible to divide the tests into groups based on:
1. report.xml of [Codeception](https://codeception.com/) library
2. Allure reports of [Yandex/Allure](https://github.com/allure-framework/allure-codeception) librariy
3. The size of the test methods (used if the first 2 do not work)

In addition, the separation depth has 3 levels:
1. Separation of directories
2. Separation of files (classes)
3. Separation by separate methods
Than smaller the division unit (method < class < directory) that more optimized the division result.

## Installation

```
composer require tzepart/test-separator
cp config.json.dist config.json
```

Add your configuration to config.json

### Pay attention
Field `level` can be one of these values:
* directory
* class
* method

Field `tests-directory` - path to directory where is yours tests

Field `result-path`- path to directory where final groups files will be

## Manual running
```
./vendor/bin/separate-tests separate 6
```

Where "6" - groups count

## Develop section
Run test
```
./vendor/bin/phpunit --bootstrap vendor/autoload.php
``` 

