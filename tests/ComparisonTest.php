<?php

namespace FindDifferent\test;

use PHPUnit\Framework\TestCase;

use function FindDifferent\comparison\genOutput;

class ComparisonTest extends TestCase
{
    public function testGenOutput()
    {
        $diffStulishFormat1 = rtrim(file_get_contents('tests/fixtures/result1.ini'));
        $diffStulishFormat2 = rtrim(file_get_contents('tests/fixtures/result2.ini'));
        $diffPlainFormat = rtrim(file_get_contents('tests/fixtures/result3.ini'));
        $diffJsonFormat = rtrim(file_get_contents('tests/fixtures/result4.ini'));
        
        $this->assertEquals($diffStulishFormat1, genOutput(
            'tests/fixtures/before.json',
            'tests/fixtures/after.json',
            'stylish'
        ));

        $this->assertEquals($diffStulishFormat2, genOutput(
            'tests/fixtures/treeBefore.json',
            'tests/fixtures/treeAfter.json',
            'stylish'
        ));

        $this->assertEquals($diffStulishFormat2, genOutput(
            'tests/fixtures/treeBefore.yml',
            'tests/fixtures/treeAfter.yml',
            'stylish'
        ));

        $this->assertEquals($diffPlainFormat, genOutput(
            'tests/fixtures/treeBefore.yml',
            'tests/fixtures/treeAfter.yml',
            'plain'
        ));
        $this->assertEquals($diffJsonFormat, genOutput(
            'tests/fixtures/treeBefore.yml',
            'tests/fixtures/treeAfter.yml',
            'json'
        ));
    }
}
