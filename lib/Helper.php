<?php

namespace iqual\DrupalSettings;

class Helper {

  /** Trusted host pattern for any host. */
  public const ANY_HOST_PATTERN = '.+';

  /**
   * Generates sensible values for the 'trusted_host_patterns' setting.
   *
   * Uses env::VIRTUAL_HOST.
   *
   * @see https://www.drupal.org/node/2410395
   *
   * @return array
   */
  public static function generateTrustedHostPatterns(array $trustedHosts = []) {
    // Encapsulated in try/catch so we don't break the system here.
    try {

      $vHosts = getenv('VIRTUAL_HOST');
      if ($vHosts === FALSE) {
        throw new \Exception('VIRTUAL_HOST does not exist or is empty.');
      }

      // Loop through vhosts, allow given host and one subdomain level.
      $vHosts = explode(',', $vHosts);
      foreach($vHosts as $vhost) {
        if (strpos($vhost, '*') !== 0) {
          $trustedHosts[] = preg_quote($vhost);
          $trustedHosts[] = '.+\.' . preg_quote($vhost);
        } else {
          // Leave catch alls alone.
          $trustedHosts[] = str_replace('*.', '.+\.', $vhost);
        }
      }

    } catch (\Exception $e) {
      fwrite(STDERR, $e->getMessage() . PHP_EOL);
    }
    finally {
      // Assert position of trusted host.
      $trustedHosts = array_unique(array_map(function($host) { return '^' . rtrim(ltrim($host, '^'), '$') . '$'; }, $trustedHosts));

      // Return patterns for trusted hosts or any host.
      return empty($trustedHosts) ? [static::ANY_HOST_PATTERN] : $trustedHosts;
    }
  }
}
