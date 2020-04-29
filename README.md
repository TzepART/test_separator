# Test Group Separator

# Installation

```
composer require tzepart/test_separator
```

## Manual running
```
./vendor/bin/separate-tests separate /path/to/file/with/allure/report/suites.csv /path/to/project/tests/ /path/to/project/file/groups/ 6
```

Where "6" - groups count

## Develop section
Run test
```
./vendor/bin/phpunit --bootstrap vendor/autoload.php
``` 
