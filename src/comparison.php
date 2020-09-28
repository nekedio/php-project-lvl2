<?php

namespace FindDifferent\comparison;

use function FindDifferent\parser\getData;
use function FindDifferent\formatters\jsonFormat\getDiffJson;
use function FindDifferent\formatters\plainFormat\getDiffPlain;
use function FindDifferent\formatters\stylishFormat\getDiffStylish;

function getOutput($first, $second, $format)
{
    $treeBefore = getTree(getData($first));
    $treeAfter = getTree(getData($second));

    $diff = getDiff($treeBefore, $treeAfter);
    
    sortTree($diff);

    if ($format === 'json') {
        $output = getDiffJson($diff);
    } elseif ($format === 'plain') {
        $output = getDiffPlain($diff);
    } elseif ($format === 'stylish' || $format === null) {
        $output = getDiffStylish($diff);
    } else {
        $output = 'gendiff: unknown format "' . $format . '"';
    }
    
    return $output;
}




function getTree($data)
{
    $data = get_object_vars($data);
    $result = array_reduce(array_keys($data), function ($acc, $key) use ($data) {
        if (is_object($data[$key])) {
            $children = getTree($data[$key]);
            $value = null;
        } else {
            $children = [];
            $value = $data[$key];
        }

        $acc[$key] = [
            'name' => $key,
            'value' => $value,
            'children' => $children
        ];
        return $acc;
    }, []);
    return $result;
}

function getDiff($treeBefore, $treeAfter)
{
    $treeMerge = array_replace_recursive($treeBefore, $treeAfter);
    $diff = function ($merge, $before, $after) use (&$diff) {
        $result = array_reduce(array_keys($merge), function ($acc, $key) use ($merge, $before, $after, $diff) {
            if ($merge[$key]['children'] != []) {
                $children = $diff(
                    $merge[$key]['children'],
                    $before[$key]['children'] ?? null,
                    $after[$key]['children'] ?? null
                );
            } else {
                $children = [];
            }
            
            $name = $key;
            $meta = null;
            $oldValue = null;
            if (($before != null) && ($after != null)) {
                if (!array_key_exists($key, $before)) {
                    $meta = 'add';
                } elseif (!array_key_exists($key, $after)) {
                    $meta = 'deleted';
                } elseif ($before != null) {
                    if ($before[$key]['value'] !== $after[$key]['value']) {
                        if ($before[$key]['value'] != null && $after[$key]['value'] != null) {
                            $meta = 'newValue';
                            $oldValue = $before[$key]['value'];
                        } elseif ($after[$key]['value'] === null) {
                            $meta = 'newValue';
                            $oldValue = $before[$key]['value'];
                        } else {
                            $meta = 'newValue';
                            $oldValue = $before[$key]['value'];
                        }
                    }
                }
            }
            $acc[$key] = getNode($key, $merge[$key]['value'], $oldValue, $meta, $children);
            return $acc;
        }, []);
        return $result;
    };

    return $diff($treeMerge, $treeBefore, $treeAfter);
}

function getNode($name, $value, $oldValue, $meta, $children)
{
    return [
        'name' => $name,
        'value' => $value,
        'oldValue' => $oldValue,
        'meta' => $meta,
        'children' => $children
    ];
}

function sortTree(&$tree)
{
    if (!is_array($tree)) {
        return;
    }
    ksort($tree);
    foreach ($tree as $key => $value) {
        sortTree($tree[$key]);
    }
    return;
}
