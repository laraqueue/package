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

**Add Facade**
```php
// config/app.php

'Laraqueue' => Laraqueue\Support\Dispatcher::class,
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

Any attribute added will be recursively removed from all job data _before_ being sent to the Laraqueue API.
```php
// config/laraqueue.php

'hidden' => [
    'password'
]

```


### Usage
Laraqueue works with started, completed, failed, and exception events out of the box. If you wish to interact with reserved events, replace job Laravel dispatches with the Laraqueue dispatcher facade or the `laraqueue` helper function.
```php
Laraqueue::dispatch(new RegisterUser($user));
```
```php
laraqueue(new RegisterUser($user));
```


### Official Documentation
Coming Soon.

### License
Laraqueue Office Package is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
