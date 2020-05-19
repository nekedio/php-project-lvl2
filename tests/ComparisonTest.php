<?php

namespace FindDifferent\test;

use PHPUnit\Framework\TestCase;
use function FindDifferent\comparison\getDiffJson;

class ComparisonTest extends TestCase
{
    public function testGetDiffJson()
    {
        $beforeJson = [
            "host" => "hexlet.io",
            "timeout" => 50,
            "proxy" => "123.234.53.22"
        ];

        $afterJson = [
            "timeout" => 20,
            "verbose" => true,
            "host" => "hexlet.io"
        ];

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

        $this->assertEquals($result1, getDiffJson($beforeJson, $afterJson));
        $this->assertEquals($result2, getDiffJson($afterJson, $beforeJson));
    }
}