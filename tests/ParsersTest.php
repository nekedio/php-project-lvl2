<?php

namespace FindDifferent\test;

use PHPUnit\Framework\TestCase;

use function FindDifferent\parser\parse;

class ParsersTest extends TestCase
{
    public function testParse()
    {
        $data = [
            'timeout' => 20,
            'verbose' => true,
            'host' => 'hexlet.io',
        ];

        $result = (object) $data;

        $this->assertEquals($result, parse('tests/fixtures/after.json'));
        $this->assertEquals($result, parse('tests/fixtures/after.yml'));
    }
}
