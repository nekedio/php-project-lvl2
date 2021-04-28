<?php

namespace GenerateDiff\test;

use PHPUnit\Framework\TestCase;

use function GenerateDiff\comparison\genOutput;

class ComparisonTest extends TestCase
{
   /**
    * @dataProvider additionProvider
    */

    public function testGenOutput(string $expected, string $before, string $after, string $format): void
    {
        $path = 'tests/fixtures/';
        $diff = rtrim((string) file_get_contents($path . $expected));
        $this->assertEquals($diff, genOutput($path . $before, $path . $after, $format));
    }

    public function additionProvider(): array
    {
        return [
            // ['diff.stylish', 'treeBefore.json', 'treeAfter.json', 'stylish'],
            ['diff.stylish', 'treeBefore.yml', 'treeAfter.yml', 'stylish'],
            ['diff.plain', 'treeBefore.yml', 'treeAfter.yml', 'plain'],
            // ['diff.json', 'treeBefore.json', 'treeAfter.json', 'json'],
        ];
    }
}
