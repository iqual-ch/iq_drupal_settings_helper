# drupal-settings-helper

## How to use

1. `composer require iqual/drupal-settings-helper`
2. Inside settings.*.php

`$settings['trusted_host_patterns'] = iqual\DrupalSettings\Helper::generateTrustedHostPatterns();`


## How to run tests

php vendor/bin/phpunit tests
