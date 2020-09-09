<?php

namespace FindDifferent\test;

use PHPUnit\Framework\TestCase;

use function FindDifferent\comparison\outputDiff;

class ComparisonTest extends TestCase
{
    public function testGetDiff()
    {
        $result1 =
            "{\n" .
            "  - follow: \n" .
            "    host: hexlet.io\n" .
            "  - proxy: 123.234.53.22\n" .
            "  - timeout: 50\n" .
            "  + timeout: 20\n" .
            "  + verbose: 1\n" .
            "}\n";

        $result2 =
            "{\n" .
            "    common: {\n" .
            "      + follow: \n" .
            "        setting1: Value 1\n" .
            "      - setting2: 200\n" .
            "      - setting3: 1\n" .
            "      + setting3: {\n" .
            "            key: value\n" .
            "        }\n" .
            "      + setting4: blah blah\n" .
            "      + setting5: {\n" .
            "            key5: value5\n" .
            "        }\n" .
            "        setting6: {\n" .
            "            doge: {\n" .
            "              - wow: too much\n" .
            "              + wow: so much\n" .
            "            }\n" .
            "            key: value\n" .
            "          + ops: vops\n" .
            "        }\n" .
            "    }\n" .
            "    group1: {\n" .
            "      - baz: bas\n" .
            "      + baz: bars\n" .
            "        foo: bar\n" .
            "      - nest: {\n" .
            "            key: value\n" .
            "        }\n" .
            "      + nest: str\n" .
            "    }\n" .
            "  - group2: {\n" .
            "        abc: 12345\n" .
            "        deep: {\n" .
            "            id: 45\n" .
            "        }\n" .
            "    }\n" .
            "  + group3: {\n" .
            "        deep: {\n" .
            "            id: {\n" .
            "                number: 45\n" .
            "            }\n" .
            "        }\n" .
            "        fee: 100500\n" .
            "    }\n" .
            "}\n";

        $this->assertEquals($result1, outputDiff(
            'json',
            'tests/fixtures/before.json',
            'tests/fixtures/after.json'
        ));

        $this->assertEquals($result1, outputDiff(
            'yml',
            'tests/fixtures/before.yml',
            'tests/fixtures/after.yml'
        ));

        $this->assertEquals($result2, outputDiff(
            'json',
            'tests/fixtures/treeBefore.json',
            'tests/fixtures/treeAfter.json'
        ));

        $this->assertEquals($result2, outputDiff(
            'yml',
            'tests/fixtures/treeBefore.yml',
            'tests/fixtures/treeAfter.yml'
        ));
    }
}
