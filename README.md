
ac-config
=========

Simple and immutable application config- and env-objects for PHP.

Install
-------

```sh
composer require oleics/ac-config
```

Usage
-----

A yaml-file `config.yaml` is used as an example:

```yaml
env:
  mode: development

foo-bar: baz
```

Config

```php
<?php

use Ac\Config\Config;

$config = Config::loadFromYaml('config.yaml');

echo $config->env->mode;      // > development
echo $config->fooBar;         // > baz

// Converts to JSON
echo $config->env.'';         // > {"mode":"development"}
echo Config::toJson($config); // > {"env":{"mode":"development"},"foo-bar":"baz"}

// Next line throws an exception
$config->env = 'production';
```

Env
```php
<?php

use Ac\Config\Env;

// maybe boot-level *hinthint*
Env::loadConfigFromYaml('config.yaml');
Env::autoset(); // throws if no valid env-mode could be detected

// maybe app-level *hinthint*
Env::configDefaults([
  'foo' => 'bar'
]);

// maybe controller-level *hinthint*
echo Env::$config->env->mode === Env::getMode(); // > true

if(Env::isNotProduction()) {
  // Something to print for non-production environments (development or staging)
  echo Env::$config->foo;    // > bar
  echo Env::$config->fooBar; // > baz
}

```

API
---

#### *final class* Config

Instances of `Config` are immutable, but you can use static methods to create/modify configurations at runtime.

##### Methods

*void* **__construct** ( array $data = null )

*array* **export** ()

##### Static methods

*Config* **factory** ( array $data = null )

*Config* **merge** ( Config $c1 [ , Config $c2, ... ])

*string* **toJson** ( Config $c1 )

*string* **toYaml** ( Config $c1 )

*bool* **writeToYaml** ( string $filepath, Config $c, $findPath = false)

*Config* **fromYaml** ( string $yaml )

*Config* **loadFromYaml** ( string $filepath, bool $findPath = false, bool $relax = false )

#### *abstract class* Env

##### Constants

MODE_DEVELOPMENT

MODE_STAGING

MODE_PRODUCTION

##### Static methods

*string* **getMode** ( )

*bool* **isKnownMode** ( string $mode )

*void* **setMode** ( string $nextMode )  
Throws an exception if `$nextMode` is invalid.

*void* **autoset** ( )  
Tries to detect a valid env-mode. Throws if detection fails.

*bool* **isNotProduction** (  )

*bool* **isProduction** (  )

*bool* **isStaging** (  )

*bool* **isDevelopment** (  )

*void* **loadConfigFromYaml** ( string $filename = 'config.yaml', bool $findPath = true, bool $relax = true)

*void* **configDefaults** ( array $defaults )

##### Static properties

*Config* **$config**  
The configuration-object loaded by `Env::loadConfigFromYaml()`


MIT License
-----------

Copyright (c) 2016 Oliver Leics <oliver.leics@gmail.com>

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
