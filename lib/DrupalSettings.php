<?php

class DrupalSettings {

  /** Trusted host pattern for any host. */
  public const ANY_HOST_PATTERN = '.+';

  /**
   * Generates sensible values for the 'trusted_host_patterns' setting.
   * 
   * Uses env::VIRTUAL_HOST.
   * 
   * @see https://www.drupal.org/node/2410395
   * 
   * @return void
   */
  public static function generateTrustedHostPatterns() {
    $trustedHosts = [];

    // Encapsulated in try/catch so we don't break the system here.
    try {
      $vHosts = getenv('VIRTUAL_HOST');
      if (!empty($vHosts)) {
        // Create array of vhosts list.
        $vHosts = explode(',', $vHosts);
        
        // Loop through vhosts, allow given host and one subdomain level.
        foreach($vHosts as $vhost) {
          if (strpos($vhost, '*') !== 0) {
            $trustedHosts[] = preg_quote($vhost);
            $trustedHosts[] = '.+\.' . preg_quote($vhost); 
          } else {
            // Leave catch alls alone.
            $trustedHosts[] = str_replace('*.', '.+\.', $vhost);
          }
        }

        // Assert position of trusted host.
        $trustedHosts = array_map(function($host) { return '^' . $host . '$'; }, $trustedHosts);
      }
    } catch (\Exception $e) {
      var_dump($e->getMessage()); 
    }
    finally {
      // Return trusted hosts or any host.
      return $trustedHosts ?? static::ANY_HOST_PATTERN;      
    }
  }
}
