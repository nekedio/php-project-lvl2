<?php

namespace GenerateDiff\comparison;

use Exception;

use function GenerateDiff\parser\parse;
use function GenerateDiff\formatters\jsonFormat\genJsonFormat;
use function GenerateDiff\formatters\plainFormat\genPlainFormat;
use function GenerateDiff\formatters\stylishFormat\genStylishFormat;
use function GenerateDiff\formatters\stylishFormat\genStylishFormat_new;

function getLeaf($key, $value1, $value2, $meta)
{
    return [
        'name' => $key,
        'value1' => $value1,
        'value2' => $value2,
        'type' => 'leaf',
        'meta' => $meta,
    ];
}

function getNode($key, $children)
{
    return [
        'name' => $key,
        'type' => 'node',
        'children' => $children,
    ];
}

function compareValue($data1, $data2, $key)
{
    if (!array_key_exists($key, $data1) && array_key_exists($key, $data2)) {
        return 'addedNode';
    }
    if (array_key_exists($key, $data1) && !array_key_exists($key, $data2)) {
        return 'removedNode';
    }
    if ($data1[$key] === $data2[$key]) {
        return 'notChangedValue';
    }
    if ($data1[$key] !== $data2[$key]) {
        return 'changedValue';
    }
    throw new Exception("The node state is not described");
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
            $acc[] = getNode($key, genDiff($value1, $value2));
        } else {
            $acc[] = getLeaf($key, $value1, $value2, $meta);
        }
 
        return $acc;
    }, []);
    return  $result;
}

function genOutput($pathToFile1, $pathToFile2, $outputFormat)
{
    $dataOfFile1 = parse(
        file_get_contents($pathToFile1),
        pathinfo($pathToFile1, PATHINFO_EXTENSION)
    );
    $dataOfFile2 = parse(
        file_get_contents($pathToFile2),
        pathinfo($pathToFile2, PATHINFO_EXTENSION)
    );
    $diff = genDiff($dataOfFile1, $dataOfFile2);
    
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
