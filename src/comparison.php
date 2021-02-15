<?php

namespace GenerateDiff\comparison;

use Exception;

use function GenerateDiff\parser\parse;
use function GenerateDiff\formatters\jsonFormat\genJsonFormat;
use function GenerateDiff\formatters\plainFormat\genPlainFormat;
use function GenerateDiff\formatters\stylishFormat\genStylishFormat;
use function GenerateDiff\formatters\stylishFormat\genStylishFormat_new;

function getLeaf($value1, $value2, $meta)
{
    return [
        'value1' => $value1,
        'value2' => $value2,
        'meta' => $meta,
        'children' => [],
    ];
}

function getNode($children)
{
    return [
        'value1' => null,
        'value2' => null,
        'meta' => null,
        'children' => $children,
    ];
}

function compareValue($data1, $data2, $key)
{
    if (!array_key_exists($key, $data1) && array_key_exists($key, $data2)) {
        return 'addNode';
    }
    if (array_key_exists($key, $data1) && !array_key_exists($key, $data2)) {
        return 'deletedNode';
    }
    if ($data1[$key] === $data2[$key]) {
        return 'notChange';
    }
    if ($data1[$key] !== $data2[$key]) {
        return 'change';
    }
    return;
}

function genDiff($objectData1, $objectData2)
{
    $data1 = get_object_vars($objectData1);
    $data2 = get_object_vars($objectData2);
    $nodeMerge = array_merge($data1, $data2);
    ksort($nodeMerge);
    $result = array_reduce(array_keys($nodeMerge), function ($acc, $key) use ($data1, $data2) {

        $value1 = $data1[$key] ?? null;
        $value2 = $data2[$key] ?? null;
        $meta = compareValue($data1, $data2, $key);

        if (is_object($value1) && is_object($value2)) {
            $acc[$key] = getNode(genDiff($value1, $value2));
        } else {
            $acc[$key] = getLeaf($value1, $value2, $meta);
        }
 
        return $acc;
    }, []);
    return  $result;
}

function getExtension(string $pathToFile)
{
    [, $extension] = explode(".", $pathToFile);
    return $extension;
}

function genOutput($pathToFile1, $pathToFile2, $outputFormat)
{
    $dataOfFile1 = parse(file_get_contents($pathToFile1), getExtension($pathToFile1));
    $dataOfFile2 = parse(file_get_contents($pathToFile2), getExtension($pathToFile2));

    $diff = genDiff($dataOfFile1, $dataOfFile2);

    //print_r($diff);

    switch ($outputFormat) {
        case 'json':
            $output = genJsonFormat($diff);
            break;
        case 'plain':
            $output = genPlainFormat($diff);
            break;
        case 'stylish':
        case null:
            $output = genStylishFormat($diff);
            break;
        default:
            throw new Exception("Unknown format '$outputFormat'");
    }
    return $output;
}
