<?php

namespace FindDifferent\comparison;

use function FindDifferent\parser\getData;
use function FindDifferent\formatters\formatJson\getDiffJson;
use function FindDifferent\formatters\formatYaml\getDiffYaml;
use function FindDifferent\formatters\formatPlain\getDiffPlain;

function outputDiff($first, $second, $format)
{
    $treeBefore = getTree(getData($first));
    $treeAfter = getTree(getData($second));

    $diff = getDiff($treeBefore, $treeAfter);

    // print_r($diff);

    if ($format === 'json') {
        $outputDiff = getDiffJson($diff);
    } elseif ($format === 'yml' || $format === 'yaml') {
        $outputDiff = getDiffYaml($diff);
    } elseif ($format === 'plain') {
        $outputDiff = getDiffPlain($diff);
    }

    return $outputDiff;
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
    sortTree($treeMerge);
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
            if (($before != null) && ($after != null)) {
                if (!array_key_exists($key, $before)) {
                    $meta = 'add';
                } elseif (!array_key_exists($key, $after)) {
                    $meta = 'deleted';
                } elseif ($before != null) {
                    if ($before[$key]['value'] !== $after[$key]['value']) {
                        if ($before[$key]['value'] != null && $after[$key]['value'] != null) {
                            $acc[] = getNode($key, $before[$key]['value'], 'oldValue', $children);
                            $meta = 'newValue';
                        } elseif ($after[$key]['value'] === null) {
                            $acc[] = getNode($key, $before[$key]['value'], 'oldValue', []);
                            $meta = 'newValue';
                        } else {
                            $acc[] = getNode($key, null, 'oldValue', $children);
                            $meta = 'newValue';
                            $children = [];
                        }
                    }
                }
            }
            $acc[] = getNode($key, $merge[$key]['value'], $meta, $children);
            return $acc;
        }, []);
        return $result;
    };

    return $diff($treeMerge, $treeBefore, $treeAfter);
}

function getNode($name, $value, $meta, $children)
{
    return [
        'name' => $name,
        'value' => $value,
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
