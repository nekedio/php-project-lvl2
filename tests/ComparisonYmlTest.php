<?php

namespace FindDifferent\test;

use PHPUnit\Framework\TestCase;
use function FindDifferent\comparisonYml\getDiffYml;

class ComparisonYmlTest extends TestCase
{
    public function testGetDiffYml()
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

        $this->assertEquals($result1, getDiffYml('tests/fixtures/before.yml', 'tests/fixtures/after.yml'));
        $this->assertEquals($result2, getDiffYml('tests/fixtures/after.yml', 'tests/fixtures/before.yml'));
    }
}