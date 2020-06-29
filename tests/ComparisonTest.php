<?php

namespace FindDifferent\test;

use PHPUnit\Framework\TestCase;

use function FindDifferent\comparison\getDiff;

class ComparisonTest extends TestCase
{
    public function testGetDiff()
    {
        $result1 = "{\n" .
            "  host: hexlet.io\n+ timeout: 20\n" .
            "- timeout: 50\n" .
            "- proxy: 123.234.53.22\n" .
            "+ verbose: 1\n}\n";

        $result2 = "{\n" .
            "  timeout: 50\n" .
            "- verbose: 1\n" .
            "  host: hexlet.io\n" .
            "+ proxy: 123.234.53.22\n}\n";

        $this->assertEquals($result1, getDiff('json', 'tests/fixtures/before.json', 'tests/fixtures/after.json'));
        $this->assertEquals($result2, getDiff('json', 'tests/fixtures/after.json', 'tests/fixtures/before.json'));
        $this->assertEquals($result1, getDiff('yml', 'tests/fixtures/before.yml', 'tests/fixtures/after.yml'));
        $this->assertEquals($result2, getDiff('yml', 'tests/fixtures/after.yml', 'tests/fixtures/before.yml'));
    }
}
