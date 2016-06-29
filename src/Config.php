<?php

namespace Ac\Config;

use \Exception;

use Ac\Common\Path;
use Ac\Common\Common;

use Symfony\Component\Yaml\Yaml;

final class Config { // (Immutable)

  private $_data = [];

  public function __construct(array $data = null) {
    if(!empty($data)) {
      $this->_import($data);
    }
  }

  public function __set($name, $value) {
    throw new Exception('Instances of Ac\Common\Config are immutable. Use the static functions to create/modify configurations.');
  }

  public function __get($name) {
    $key = Common::toDash($name);
    if(!isset($this->_data[$key])) {
      return;
    }
    $val = $this->_data[$key];
    if(is_array($val) && Common::is_assoc($val)) {
      $val = self::factory($val);
      return $val;
    }
    return $val;
  }

  public function __isset($name) {
    return isset($this->_data[Common::toDash($name)]);
  }

  public function __unset($name) {
    unset($this->_data[Common::toDash($name)]);
  }

  public function __toString() {
    return self::toJson($this);
  }

  //

  private function _import(array $data) {
    $this->_data = Common::array_merge_recursive($this->_data, $data);
  }

  public function export() {
    return $this->_data;
  }

  //// Static

  public static function factory(array $data = null) {
    return new Config($data);
  }

  public static function merge(Config $c1, Config $c2 /* , ... */) {
    $c = self::factory();
    foreach(func_get_args() as $cX) {
      $c->_data = Common::array_merge_recursive($c->_data, $cX->_data);
    }
    return $c;
  }

  // Format: Json

  public static function toJson(Config $c) {
    if(empty($c->_data)) return '{}';
    return json_encode($c->_data);
  }

  // Format: Yaml

  public static function toYaml(Config $c) {
    return Yaml::dump($c->_data);
  }

  public static function writeToYaml($p, Config $c, $findPath = false) {
    if($findPath) {
      $p = Path::find($p);
      if(!$p) {
        throw new Exception('Could not find config-yaml-file: '.func_get_arg(0));
      }
    }
    return !! file_put_contents($p, self::toYaml($c));
  }

  public static function fromYaml($yaml) {
    return self::factory(Yaml::parse($yaml));
  }

  public static function loadFromYaml($p, $findPath = false, $relax = false) {
    if($findPath) {
      $p = Path::find($p);
      if(!$p && !$relax) {
        throw new Exception('Could not find config-yaml-file: '.func_get_arg(0));
      }
    }
    if($p) {
      $p = file_get_contents($p);
      if($p === false) {
        throw new Exception('Could not load config from yaml-file: '.func_get_arg(0));
      }
      return self::fromYaml($p);
    }
    return self::factory();
  }

}
