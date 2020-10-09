# Test Group Separator

## Description:
It is possible to divide the tests into groups based on:
1. report.xml of [Codeception](https://codeception.com/) library
1. The size of the test methods (used if the first 2 do not work)

In addition, the separation depth has 3 levels:
1. Separation of directories
2. Separation of files (classes)
3. Separation by separate methods
Than smaller the division unit (method < class < directory) that more optimized the division result.

## Installation

```
composer require tzepart/test-separator
```

### Configuration

Add configuration file `config/test_separator.yml`, which contents:
```yaml
test_separator:
  separating-strategy: 'codeception-report'
  use-default-separating-strategy: false
  codeception-reports-directory: '/path/to/file/with/codeception/test/'
  tests-directory: '/path/to/project/tests/'
  result-path: '/path/to/project/file/groups/'
  level: 'method'
  test-suites-directories:
      - 'list'
      - 'sub-directories'
      - 'with'
      - 'test-suites'
```

Parameter **separating-strategy** can be one of these values:
* codeception-report
* method-size

If parameter **use-default-separating-strategy: true** than, if we can't use **codeception-report** strategy we'll try use default strategy (**method-size**)

Parameter **tests-directory** - path to directory where is yours tests

Parameter **result-path** - path to directory where final groups files will be

Parameter **level** can be one of these values:
* directory
* class
* method

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
