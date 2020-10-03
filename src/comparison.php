<?php

namespace FindDifferent\comparison;

use function FindDifferent\parser\parse;
use function FindDifferent\formatters\jsonFormat\genJsonFormat;
use function FindDifferent\formatters\plainFormat\genPlainFormat;
use function FindDifferent\formatters\stylishFormat\genStylishFormat;

function genOutput($pathToFile1, $pathToFile2, $outputFormat)
{
    $dataOfFile1 = parse($pathToFile1);
    $dataOfFile2 = parse($pathToFile2);
    
    $tree1 = genTree($dataOfFile1);
    $tree2 = genTree($dataOfFile2);

    $diff = genDiff($tree1, $tree2);
    $sortDiff = sortTree($diff);

    // print_r($diff);

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
            $output = 'gendiff: unknown format "' . $outputFormat . '"';
    }
    return $output;
}

function genTree($object)
{
    $data = get_object_vars($object);
    $tree = array_reduce(array_keys($data), function ($acc, $key) use ($data) {
        if (is_object($data[$key])) {
            $children = genTree($data[$key]);
            $value = null;
        } else {
            $children = [];
            $value = $data[$key];
        }

        $acc[$key] = [
            //'name' => $key,
            'value' => $value,
            'children' => $children
        ];
        return $acc;
    }, []);
    return $tree;
}

function genDiff($tree1, $tree2)
{
    $treeMerge = array_replace_recursive($tree1, $tree2);
    $diff = traversalMerge($treeMerge, $tree1, $tree2);
    return $diff;
}

function traversalMerge($nodeMerge, $node1, $node2)
{
    $result = array_reduce(array_keys($nodeMerge), function ($acc, $key) use ($nodeMerge, $node1, $node2) {
        if ($nodeMerge[$key]['children'] != []) {
            $children = traversalMerge(
                $nodeMerge[$key]['children'],
                $node1[$key]['children'] ?? null,
                $node2[$key]['children'] ?? null
            );
        } else {
            $children = [];
        }

        if (($node1 != null) && ($node2 != null)) {
            $acc[$key] = getNode($nodeMerge[$key], $node1[$key] ?? null, $node2[$key] ?? null, $children);
        } else {
            $acc[$key] = [
                'value' => $nodeMerge[$key]['value'],
                'oldValue' => null,
                'meta' => null,
                'children' => $children
            ];
        }
        return $acc;
    }, []);
    return $result;
}

function getNode($nodeMerge, $node1, $node2, $children)
{
    $meta = null;
    $oldValue = null;
    if ($node1 === null) {
        $meta = 'add';
    } elseif ($node2 === null) {
        $meta = 'deleted';
    } elseif ($node1 != null) {
        if ($node1['value'] !== $node2['value']) {
            if ($node1['value'] != null && $node2['value'] != null) {
                $meta = 'newValue';
                $oldValue = $node1['value'];
            } elseif ($node2['value'] === null) {
                $meta = 'newValue';
                $oldValue = $node1['value'];
            } else {
                $meta = 'newValue';
                $oldValue = $node1['value'];
            }
        }
    }
    return [
        'value' => $nodeMerge['value'],
        'oldValue' => $oldValue,
        'meta' => $meta,
        'children' => $children
    ];
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
