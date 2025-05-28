
# sourecode/timezone-bundle

[![Packagist Version](https://img.shields.io/packagist/v/sourecode/timezone-bundle.svg)](https://packagist.org/packages/sourecode/timezone-bundle)
[![Downloads](https://img.shields.io/packagist/dt/sourecode/timezone-bundle.svg)](https://packagist.org/packages/sourecode/timezone-bundle)
[![CI](https://github.com/SoureCode/TimezoneBundle/actions/workflows/ci.yml/badge.svg?branch=master)](https://github.com/SoureCode/TimezoneBundle/actions/workflows/ci.yml)
[![codecov](https://codecov.io/gh/SoureCode/TimezoneBundle/branch/master/graph/badge.svg?token=3862YZNG87)](https://codecov.io/gh/SoureCode/TimezoneBundle)
[![Mutation testing badge](https://img.shields.io/endpoint?style=flat&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2FSoureCode%2FTimezoneBundle%2Fmaster)](https://dashboard.stryker-mutator.io/reports/github.com/SoureCode/TimezoneBundle/master)


This bundle provides a simple way to manage timezones in Symfony applications.
Unfortunately, there is no way to set the timezone in the request object, so you have to get it from the `TimezoneManager`.
Except from that, the usage is pretty simple and the same as for the locale.

- [License](./LICENSE)

## Installation

Make sure Composer is installed globally, as explained in the
[installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

### Applications that use Symfony Flex

Open a command console, enter your project directory and execute:

```console
composer require sourecode/timezone-bundle
```

### Applications that don't use Symfony Flex

#### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
composer require sourecode/timezone-bundle
```

#### Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles
in the `config/bundles.php` file of your project:

```php
// config/bundles.php

return [
    // ...
    \SoureCode\Bundle\Timezone\SoureCodeTimezoneBundle::class => ['all' => true],
];
```

## Config

```yaml
# config/packages/soure_code_timezone.yaml
soure_code_timezone:
    # if default_timezone is empty, the default timezone is 'UTC'
    default_timezone: 'Asia/Tokyo'
    # if enabled_timezones is empty, all timezones are enabled
    enabled_timezones: ['UTC', 'Europe/Berlin', 'Asia/Tokyo', 'Australia/Sydney']
```