<?php

namespace FindDifferent\test;

use PHPUnit\Framework\TestCase;

use function FindDifferent\comparison\genOutput;

class ComparisonTest extends TestCase
{
    public function testGenOutput()
    {
        $result1 =
            "{\n" .
            "  - !follow: false\n" .
            "    host: hexlet.io\n" .
            "  - proxy: 123.234.53.22\n" .
            "  - timeout: 50\n" .
            "  + timeout: 20\n" .
            "  + verbose: true\n" .
            "}";

        $result2 =
            "{\n" .
            "    common: {\n" .
            "      + follow: false\n" .
            "        setting1: Value 1\n" .
            "      - setting2: 200\n" .
            "      - setting3: true\n" .
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
            "}";

        $result3 =
            "Property 'common.follow' was added with value: false\n" .
            "Property 'common.setting2' was removed\n" .
            "Property 'common.setting3' was updated. From true to [complex value]\n" .
            "Property 'common.setting4' was added with value: 'blah blah'\n" .
            "Property 'common.setting5' was added with value: [complex value]\n" .
            "Property 'common.setting6.doge.wow' was updated. From 'too much' to 'so much'\n" .
            "Property 'common.setting6.ops' was added with value: 'vops'\n" .
            "Property 'group1.baz' was updated. From 'bas' to 'bars'\n" .
            "Property 'group1.nest' was updated. From [complex value] to 'str'\n" .
            "Property 'group2' was removed\n" .
            "Property 'group3' was added with value: [complex value]";

        $result4 = '{"common":{"follow":"addedKey","setting1":"noChange",' .
            '"setting2":"deletedKey","setting3":"addedChildren","setting4":' .
            '"addedKey","setting5":"addedKey","setting6":{"doge":{"wow":"changedValue"}' .
            ',"key":"noChange","ops":"addedKey"}},"group1":{"baz":"changedValue",' .
            '"foo":"noChange","nest":"deletedChildren"},"group2":"deletedKey",' .
            '"group3":"addedKey"}';


        $this->assertEquals($result1, genOutput(
            'tests/fixtures/before.json',
            'tests/fixtures/after.json',
            'stylish'
        ));

        $this->assertEquals($result2, genOutput(
            'tests/fixtures/treeBefore.json',
            'tests/fixtures/treeAfter.json',
            'stylish'
        ));

        $this->assertEquals($result2, genOutput(
            'tests/fixtures/treeBefore.yml',
            'tests/fixtures/treeAfter.yml',
            'stylish'
        ));

        $this->assertEquals($result3, genOutput(
            'tests/fixtures/treeBefore.yml',
            'tests/fixtures/treeAfter.yml',
            'plain'
        ));
        $this->assertEquals($result4, genOutput(
            'tests/fixtures/treeBefore.yml',
            'tests/fixtures/treeAfter.yml',
            'json'
        ));
    }
}
