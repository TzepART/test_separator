# Test Group Separator

# Installation

```
composer require tzepart/test-separator
cp config.json.dist config.json
```

Add your configuration to config.json

###Pay attention
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
