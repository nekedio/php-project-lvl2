<?php

namespace CompareTool\comparison;

use Exception;

use function CompareTool\parser\parse;
use function CompareTool\formatters\jsonFormat\genJsonFormat;
use function CompareTool\formatters\plainFormat\genPlainFormat;
use function CompareTool\formatters\stylishFormat\genStylishFormat;

function genOutput($pathToFile1, $pathToFile2, $outputFormat)
{
    $dataOfFile1 = parse(file_get_contents($pathToFile1), getExtension($pathToFile1));
    $dataOfFile2 = parse(file_get_contents($pathToFile2), getExtension($pathToFile2));
    
    $diff = genDiff($dataOfFile1, $dataOfFile2);
    $sortDiff = sortTree($diff);

    switch ($outputFormat) {
        case 'json':
            $output = genJsonFormat($sortDiff);
            break;
        case 'plain':
            $output = genPlainFormat($sortDiff);
            break;
        case 'stylish':
        case null:
            $output = genStylishFormat($sortDiff);
            break;
        default:
            throw new Exception("Unknown format '$outputFormat'");
    }
    return $output;
}

function getExtension(string $pathToFile)
{
    [, $extension] = explode(".", $pathToFile);
    return $extension;
}

function genDiff($objectData1, $objectData2)
{
    $data1 = get_object_vars($objectData1);
    $data2 = get_object_vars($objectData2);
    $nodeMerge = array_merge($data1, $data2);
    $result = array_reduce(array_keys($nodeMerge), function ($acc, $key) use ($data1, $data2) {
        $children = getChildren($data1, $data2, $key);
        if ($children === []) {
            $acc[$key] = getLeaf($data1, $data2, $key);
        } else {
            $acc[$key] = getNode($data1, $data2, $key, $children);
        }
        return $acc;
    }, []);
    return  $result;
}

function getChildren($data1, $data2, $key)
{
    $value1 = $data1[$key] ?? null;
    $value2 = $data2[$key] ?? null;
    
    if (is_object($value1) && is_object($value2)) {
        return genDiff($value1, $value2);
    }
    
    return [];
}

function getLeaf($data1, $data2, $key)
{
    return [
        'value1' => $data1[$key] ?? null,
        'value2' => $data2[$key] ?? null,
        'meta' => genMeta($data1, $data2, $key),
        'children' => [],
    ];
}

function getNode($data1, $data2, $key, $children)
{
    return [
        'value1' => null,
        'value2' => null,
        'meta' => genMeta($data1, $data2, $key),
        'children' => $children,
    ];
}

function genMeta($data1, $data2, $key)
{
    if (!array_key_exists($key, $data1) && array_key_exists($key, $data2)) {
        return 'addNode';
    }
    if (array_key_exists($key, $data1) && !array_key_exists($key, $data2)) {
        return 'deletedNode';
    }
    return;
}


function sortTree($node)
{
    ksort($node);
    $result = array_reduce(array_keys($node), function ($acc, $key) use ($node) {
            $acc[$key] = $node[$key];
        if ($node[$key]['children'] != []) {
            $acc[$key]['children'] = sortTree($node[$key]['children']);
        }
        return $acc;
    }, []);
    return $result;
}
