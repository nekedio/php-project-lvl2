<?php

namespace FindDifferent\comparison;

use function FindDifferent\parser\getData;
use function Funct\Strings\times;

function outputDiff($format, $first, $second)
{
/*       format required for output
 *
    if ($format === 'json' || $format === 'pretty') {
        $dataFirst = getDataFromJson($first);
        $dataSecond = getDataFromJson($second);
    } elseif ($format === 'yml') {
        $dataFirst = getDataFromYaml($first);
        $dataSecond = getDataFromYaml($second);
    }
*/

    $treeBefore = getTree(getData($first));
    $treeAfter = getTree(getData($second));

    $diff = getDiff($treeBefore, $treeAfter);
    $strDiff = getDiffString($diff);

    //print_r($treeBefore);

    return $strDiff;
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
                            $acc[] = getNode($key, $before[$key]['value'], 'deleted', $children);
                            $meta = 'add';
                        } elseif ($after[$key]['value'] === null) {
                            $acc[] = getNode($key, $before[$key]['value'], 'deleted', []);
                            $meta = 'add';
                        } else {
                            $acc[] = getNode($key, null, 'deleted', $children);
                            $meta = 'add';
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

function getDiffString($diff)
{
    $diffString = function ($diff, $depth = 1) use (&$diffString) {
        $result = array_reduce(array_keys($diff), function ($acc, $key) use ($diff, $depth, $diffString) {
            $indent = getIndent($diff[$key]['meta'], $depth);
            if ($diff[$key]['value'] === null) {
                $acc[] = $indent['open'] . $diff[$key]['name'] . ": {";
            } else {
                $acc[] = $indent['open'] . $diff[$key]['name'] . ": " . $diff[$key]['value'];
            }
            if ($diff[$key]['children'] != []) {
                $acc[] = $diffString($diff[$key]['children'], $depth + 1);
                $acc[] = $indent['close'] . "}";
            }
            return $acc;
        }, []);
        return implode("\n", $result);
    };
    $result = "{\n" . $diffString($diff) . "\n}\n";
    return $result;
}

function getIndent($meta, $depth)
{
    if ($meta === 'add') {
        $indentOpen =  times("    ", $depth - 1) . "  + ";
    } elseif ($meta === 'deleted') {
         $indentOpen = times("    ", $depth - 1) . "  - ";
    } else {
        $indentOpen = times("    ", $depth);
    }
    $result = [
        'open' => $indentOpen,
        'close' => times("    ", $depth)
    ];
    return $result;
}
