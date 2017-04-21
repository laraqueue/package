# Laraqueue Official Package

[![Latest Stable Version](https://poser.pugx.org/laraqueue/package/version)](https://packagist.org/packages/laraqueue/package)
[![Total Downloads](https://poser.pugx.org/laraqueue/package/downloads)](https://packagist.org/packages/laraqueue/package)
[![License](https://poser.pugx.org/laraqueue/package/license)](https://packagist.org/packages/laraqueue/package)

### Introduction

This is the official Laravel package for Laraqueue, the real-time queue management tool for Laravel applications.

### Installation

**Require via composer**
```bash
composer require laraqueue/package
```

**Add Service Provider**
```php
// config/app.php

Laraqueue\Package\LaraqueueServiceProvider::class,
```

**Publish**
```php
php artisan vendor:publish --provider="Laraqueue\Providers\LaraqueueServiceProvider"
```

### Configuration
**Add App Key**
```bash
# .env

LARAQUEUE_KEY=<your key here>
```

**Add Hidden Model Attributes**

Any attribute added will be recursively removed from all job data _before_ being sent to the Laraqueue API. By default, `password` is always hidden.
```php
// config/laraqueue.php

'hidden' => [
    'password'
]

```

### Usage
**That's it!** Laraqueue overrides the Laravel `BusServiceProvider::dispatch` method to report all non-sync jobs.


### Official Documentation
Coming Soon.

### License
Laraqueue Office Package is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
