<?php

namespace FindDifferent\test;

use PHPUnit\Framework\TestCase;
use function FindDifferent\comparisonJson\getDiffJson;

class ComparisonJsonTest extends TestCase
{
    public function testGetDiffJson()
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

        $this->assertEquals($result1, getDiffJson('tests/fixtures/before.json', 'tests/fixtures/after.json'));
        $this->assertEquals($result2, getDiffJson('tests/fixtures/after.json', 'tests/fixtures/before.json'));
    }
}