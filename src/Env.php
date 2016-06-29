<?php

namespace Ac\Config;

use \Exception;

use Ac\Config\Config;

abstract class Env {

  // Mode

  const MODE_DEVELOPMENT = 'development';
  const MODE_STAGING     = 'staging';
  const MODE_PRODUCTION  = 'production';

  private static $_mode = null;

  public static function getMode() {
    return self::$_mode;
  }

  public static function isKnownMode($mode) {
    if(
        $mode === self::MODE_PRODUCTION
        || $mode === self::MODE_STAGING
        || $mode === self::MODE_DEVELOPMENT
    ) {
      return true;
    }
    return false;
  }

  public static function setMode($nextMode) {
    if(!self::isKnownMode($nextMode)) {
      throw new Exception('Unknown env-mode: '.$nextMode);
    }
    self::$_mode = $nextMode;
  }

  public static function autoset() {
    if(isset(self::$_mode)) return;
    if(@$_SERVER['SERVER_NAME'] === 'localhost') {
      return self::setMode(self::MODE_DEVELOPMENT);
    }
    if(isset($_ENV['PHP_ENV'])) {
      return self::setMode($_ENV['PHP_ENV']);
    }
    if(isset(self::$config->env->mode)) {
      return self::setMode(self::$config->env->mode);
    }
    throw new Exception('Unable to set env-mode.');
    return self::setMode(self::MODE_DEVELOPMENT);
  }

  public static function isNotProduction() {
    return self::$_mode !== self::MODE_PRODUCTION;
  }

  public static function isProduction() {
    return self::$_mode === self::MODE_PRODUCTION;
  }

  public static function isStaging() {
    return self::$_mode === self::MODE_STAGING;
  }

  public static function isDevelopment() {
    return self::$_mode === self::MODE_DEVELOPMENT;
  }

  // Config

  public static $config = null;

  public static function loadConfigFromYaml($filename = 'config.yaml', $findPath = true, $relax = true) {
    self::$config = Config::loadFromYaml($filename, $findPath, $relax);
  }

  public static function configDefaults(array $defaults) {
    self::$config = Config::merge(Config::factory($defaults), self::$config);
  }

}
