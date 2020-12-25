<?php

namespace CompareTool\test;

use PHPUnit\Framework\TestCase;

use function CompareTool\comparison\genOutput;

class ComparisonTest extends TestCase
{
   /**
    * @dataProvider additionProvider
    */

    public function testGenOutput($expected, $before, $after, $format)
    {
        $diffStulishFormat1 = rtrim(file_get_contents($expected));
        
        $this->assertEquals($diffStulishFormat1, genOutput(
            $before,
            $after,
            $format
        ));
    }

    public function additionProvider()
    {
        return [
            //['tests/fixtures/result1.diff', 'tests/fixtures/before.json', 'tests/fixtures/after.json', 'stylish'],
            ['tests/fixtures/result2.diff', 'tests/fixtures/treeBefore.json', 'tests/fixtures/treeAfter.json', 'stylish'],
            //['tests/fixtures/result2.diff', 'tests/fixtures/treeBefore.yml', 'tests/fixtures/treeAfter.yml', 'stylish'],
            //['tests/fixtures/result3.diff', 'tests/fixtures/treeBefore.yml', 'tests/fixtures/treeAfter.yml', 'plain'],
            //['tests/fixtures/result4.diff', 'tests/fixtures/treeBefore.json', 'tests/fixtures/treeAfter.json', 'json'],
        ];
    }
}
