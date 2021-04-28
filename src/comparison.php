<?php

namespace GenerateDiff\comparison;

use Exception;

use function GenerateDiff\parser\parse;
use function GenerateDiff\formatters\jsonFormat\genJsonFormat;
use function GenerateDiff\formatters\plainFormat\genPlainFormat;
use function GenerateDiff\formatters\stylishFormat\genStylishFormat;

function genDiff(object $objectData1, object $objectData2): array
{
    $data1 = get_object_vars($objectData1);
    $data2 = get_object_vars($objectData2);
    $nodeMerge = array_merge($data1, $data2);
    ksort($nodeMerge);
    $result = array_reduce(array_keys($nodeMerge), function ($acc, $key) use ($data1, $data2) {
        $value1 = $data1[$key] ?? null;
        $value2 = $data2[$key] ?? null;

        if (is_object($value1) && is_object($value2)) {
            $acc[] = ['name' => $key, 'type' => 'node', 'children' => gendiff($value1, $value2)];
            return $acc;
        }
        if (!array_key_exists($key, $data1) && array_key_exists($key, $data2)) {
            $acc[] = ['name' => $key, 'value1' => $value1, 'value2' => $value2, 'type' => 'added'];
            return $acc;
        }
        if (array_key_exists($key, $data1) && !array_key_exists($key, $data2)) {
            $acc[] = ['name' => $key, 'value1' => $value1, 'value2' => $value2, 'type' => 'removed'];
            return $acc;
        }
        if ($data1[$key] === $data2[$key]) {
            $acc[] = ['name' => $key, 'value1' => $value1, 'value2' => $value2, 'type' => 'notChangedValue'];
            return $acc;
        }
        if ($data1[$key] !== $data2[$key]) {
            $acc[] = ['name' => $key, 'value1' => $value1, 'value2' => $value2, 'type' => 'changedValue'];
            return $acc;
        }
    }, []);
    return  $result;
}

function genOutput(string $pathToFile1, string $pathToFile2, string $outputFormat): string
{
    $content1 = (string) file_get_contents($pathToFile1);

    $dataOfFile1 = parse(
        pathinfo($pathToFile1, PATHINFO_EXTENSION),
        (string) file_get_contents($pathToFile1)
    );

    $dataOfFile2 = parse(
        pathinfo($pathToFile2, PATHINFO_EXTENSION),
        (string) file_get_contents($pathToFile2)
    );
    $diff = genDiff($dataOfFile1, $dataOfFile2);

    // print_r($diff);

    switch ($outputFormat) {
        case 'json':
            $output = genJsonFormat($diff);
            break;
        case 'plain':
            $output = genPlainFormat($diff);
            break;
        case 'stylish':
            $output = genStylishFormat($diff);
            break;
        default:
            throw new Exception("Unknown format '$outputFormat'");
    }
    return $output;
}
