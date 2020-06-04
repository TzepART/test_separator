# Test Group Separator

# Installation

```
composer require tzepart/test-separator
cp config.json.dist config.json
```

Add your configuration to config.json

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
