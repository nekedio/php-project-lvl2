<?php

namespace FindDifferent\test;

use PHPUnit\Framework\TestCase;

use function FindDifferent\parsers\getDataFromJson;
use function FindDifferent\parsers\getDataFromYaml;

class ParsersTest extends TestCase
{
    public function testGetDataFromJson()
    {
        $data = [
            'timeout' => 20,
            'verbose' => 1,
            'host' => 'hexlet.io',
        ];

        $result = (object) $data;

        $this->assertEquals($result, getDataFromJson('tests/fixtures/after.json'));
    }

    public function testGetDataFromYaml()
    {
        $data = [
            'timeout' => 20,
            'verbose' => 1,
            'host' => 'hexlet.io',
        ];

        $result = (object) $data;

        $this->assertEquals($result, getDataFromYaml('tests/fixtures/after.yml'));
    }
}
