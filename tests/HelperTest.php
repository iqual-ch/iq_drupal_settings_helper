<?php

namespace iqual\DrupalSettings;

use PHPUnit\Framework\TestCase;

class HelperTest extends TestCase {

    public function testEmptyVirtualHostAndEmptyInputArray()
    {
        $this->assertEqualsCanonicalizing(
            [
                Helper::ANY_HOST_PATTERN,
            ],
            Helper::generateTrustedHostPatterns(),
            "Expected 'Any host' pattern for empty VIRTUAL_HOST & input array."
        );
    }

    public function testEmptyVirtualHostAndPopulatedInputArrayWithDuplicates()
    {
        putenv('VIRTUAL_HOST');
        $this->assertEqualsCanonicalizing(
            [
                '^example\.com$',
                '^yahoo\.com$',
            ],
            Helper::generateTrustedHostPatterns([
                '^example\.com$',
                'yahoo\.com',
                'yahoo\.com$',
                '^yahoo\.com',
                '^yahoo\.com$',
            ]),
            "Expected updated pattern(s) only from populated input array."
        );
    }

    public function testSingularVirtualHostAndEmptyInputArray()
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

    public function testSingularVirtualHostAndPopulatedInputArray()
    {
        putenv('VIRTUAL_HOST=example.com');
        $this->assertEqualsCanonicalizing(
            [
                '^.+\.example\.com$',
                '^example\.com$',
                '^iqual\.ch$',
            ],
            Helper::generateTrustedHostPatterns([
                'iqual\.ch$',
            ]),
            "Expected 2 patterns for example.com and one from input array."
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

    public function testMultipleVirtualHostsAndPopulatedInputArray()
    {
        putenv('VIRTUAL_HOST=example.com,counter-example.com');
        $this->assertEqualsCanonicalizing(
            [
                '^.+\.example\.com$',
                '^example\.com$',
                '^.+\.counter\-example\.com$',
                '^counter\-example\.com$',
                '^iqual\.ch$',
            ],
            Helper::generateTrustedHostPatterns([
                '^iqual\.ch$',
            ]),
            "Expected 2 patterns for each of example.com and counter-example.com and 1 from input array"
        );
    }
}
