<?php

namespace iqual\DrupalSettings;

use PHPUnit\Framework\TestCase;

class HelperTest extends TestCase {

    public function testEmptyVirtualHost()
    {
        putenv('VIRTUAL_HOST');
        $this->assertEqualsCanonicalizing(
            [
                Helper::ANY_HOST_PATTERN,
            ],
            Helper::generateTrustedHostPatterns(),
            "Expected 'Any host' pattern."
        );
    }

    public function testSingularVirtualHost()
    {
        putenv('VIRTUAL_HOST=example.com');
        $this->assertEqualsCanonicalizing(
            [
                '^.+\.example\.com$',
                '^example\.com$',
            ],
            Helper::generateTrustedHostPatterns(),
            "Expected 2 patterns for example.com."
        );
    }

    public function testMultipleVirtualHosts()
    {
        putenv('VIRTUAL_HOST=example.com,counter-example.com');
        $this->assertEqualsCanonicalizing(
            [
                '^.+\.example\.com$',
                '^example\.com$',
                '^.+\.counter\-example\.com$',
                '^counter\-example\.com$',
            ],
            Helper::generateTrustedHostPatterns(),
            "Expected 2 patterns for each of example.com and counter-example.com."
        );
    }
    
}
