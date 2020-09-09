<?php

namespace FindDifferent\test;

use PHPUnit\Framework\TestCase;

use function FindDifferent\parser\getData;

class ParsersTest extends TestCase
{
    public function testGetData()
    {
        $data = [
            'timeout' => 20,
            'verbose' => 1,
            'host' => 'hexlet.io',
        ];

        $result = (object) $data;

        $this->assertEquals($result, getData('tests/fixtures/after.json'));
        $this->assertEquals($result, getData('tests/fixtures/after.yml'));
    }
}
