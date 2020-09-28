<?php

namespace FindDifferent\formatters\stylishFormat;

use function Funct\Strings\times;

function getDiffStylish($diff)
{
    return fff($diff);
}

function fff($diff, $depth = 1)
{
    $indent = getIndent($depth);
    $result = array_reduce(array_keys($diff), function ($acc, $key) use ($diff, $indent, $depth) {
        if ($diff[$key]['children'] === []) {
            $acc[] = getStrValue($key, $diff[$key], $indent);
        } else {
            $acc[] = getStrKey($key, $diff[$key], $indent, $depth);
        }
        return $acc;
    }, []);
    if (is_array($diff)) {
        array_unshift($result, "{");
        $result[] = $indent['close'] . "}";
    }
    return implode("\n", $result);
}

function getStrKey($key, $node, $indent, $depth)
{
    if ($node['meta'] === 'add') {
        $result = $indent['openAdd'] . $key . ": " . fff($node['children'], $depth + 1);
    } elseif ($node['meta'] === 'deleted') {
        $result = $indent['openDel'] . $key . ": " . fff($node['children'], $depth + 1);
    } elseif ($node['meta'] === 'newValue') {
        if ($node['value'] === null) {
            $result1 = $indent['openDel'] . $key . ": " . $node['oldValue'];
            $result2 = $indent['openAdd'] . $key . ": " . fff($node['children'], $depth + 1);
            $result = implode("\n", [$result1, $result2]);
        } else {
            $result1 = $indent['openDel'] . $key . ": " . fff($node['children'], $depth + 1);
            $result2 = $indent['openAdd'] . $key . ": " . $node['value'];
            $result = implode("\n", [$result1, $result2]);
        }
    } else {
        $result = $indent['standart'] . $key . ": " . fff($node['children'], $depth + 1);
    }

    return $result;
}

function getStrValue($key, $node, $indent)
{
    if ($node['meta'] === 'add') {
        $result = $indent['openAdd'] . $key . ": " . $node['value'];
    } elseif ($node['meta'] === 'deleted') {
        $result = $indent['openDel'] . $key . ": " . $node['value'];
    } elseif ($node['meta'] === 'newValue') {
        $result1 = $indent['openDel'] . $key . ": " . $node['oldValue'];
        $result2 = $indent['openAdd'] . $key . ": " . $node['value'];
        $result = implode("\n", [$result1, $result2]);
    } else {
        $result = $indent['standart'] . $key . ": " . $node['value'];
    }

    return $result;
}

function getIndent($depth)
{
    return [
        'standart' => times("    ", $depth),
        'close' => times("    ", $depth - 1),
        'openAdd' => times("    ", $depth - 1) . "  + ",
        'openDel' => times("    ", $depth - 1) . "  - "
    ];
}
