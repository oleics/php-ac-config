<?php

require_once __DIR__ . '/../vendor/autoload.php';

use \Exception;

use Ac\Config\Config;
use Ac\Config\Env;

define('TEST_CONFIG_YAML_FILE', 'test/config.yaml');

//
$yaml = <<<EOT
foo-bar: baz
foo:
  bar:
    baz
EOT;

//
$c1 = Config::fromYaml($yaml);
assert(Config::toJson($c1) === '{"foo-bar":"baz","foo":{"bar":"baz"}}');
assert($c1->fooBar === 'baz');
assert($c1->foo.'' === '{"bar":"baz"}');
assert($c1->foo->bar === 'baz');

//
$c2 = Config::factory(['bar' => 'baz']);
assert(Config::toJson($c2) === '{"bar":"baz"}');

//
$c3 = Config::merge($c1, $c2);
assert(Config::toJson($c3) === '{"foo-bar":"baz","foo":{"bar":"baz"},"bar":"baz"}');

//
$c4 = Config::loadFromYaml(TEST_CONFIG_YAML_FILE, true);
assert(isset($c4->env->mode));
assert(isset($c4->emails->production));
assert($c4->emails->development[0] === 'oliver.leics@gmail.com');

//
try {
  $c4->env->mode = 'forbidden';
  assert(false);
} catch(Exception $e) {}

//
try {
  $c5 = Config::loadFromYaml('--not-existing-config.yaml--', true);
  assert(false);
} catch(Exception $e) { }

//
$c6 = Config::loadFromYaml('--not-existing-config.yaml--', true, true);
assert(Config::toJson($c6) === '{}');

// Env::$config

Env::loadConfigFromYaml(TEST_CONFIG_YAML_FILE);
Env::autoset();

Env::configDefaults([
  'foo' => [
    'bar' => [
      'baz' => 1000,
      'buz' => './some.json',
    ]
  ],
  'bar' => [
    'baz' => 180
  ]
]);

assert(Env::$config->env->mode === Env::getMode());
assert(count(Env::$config->emails->production) === 1);
assert(count(Env::$config->emails->development) === 1);
assert(Env::$config->foo->bar->baz === 1000);
assert(Env::$config->foo->bar->buz === './some.json');
assert(Env::$config->bar->baz === 360);
