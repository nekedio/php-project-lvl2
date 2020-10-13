<?php

namespace CompareTool\formatters\stylishFormat;

use function Funct\Strings\times;
use function CompareTool\formatters\additionalFunc\showBoolValue;

function genStylishFormat($tree, $depth = 1)
{
    $indent = genIndent($depth);
    $result = array_reduce(array_keys($tree), function ($acc, $key) use ($tree, $indent, $depth) {
        if ($tree[$key]['children'] === []) {
            $acc[] = genValueToLine($key, $tree[$key], $indent);
        } else {
            $acc[] = genKeyToLine($key, $tree[$key], $indent, $depth);
        }
        return $acc;
    }, []);
    if (is_array($tree)) {
        array_unshift($result, "{");
        $result[] = $indent['close'] . "}";
    }
    return implode("\n", $result);
}

function genKeyToLine($key, $node, $indent, $depth)
{
    if ($node['meta'] === 'add') {
        $result = $indent['openAdd'] . $key . ": " . genStylishFormat($node['children'], $depth + 1);
    } elseif ($node['meta'] === 'deleted') {
        $result = $indent['openDel'] . $key . ": " . genStylishFormat($node['children'], $depth + 1);
    } elseif ($node['meta'] === 'newValue') {
        if ($node['value'] === null) {
            $result1 = $indent['openDel'] . $key . ": " . showBoolValue($node['oldValue']);
            $result2 = $indent['openAdd'] . $key . ": " . genStylishFormat($node['children'], $depth + 1);
            $result = implode("\n", [$result1, $result2]);
        } else {
            $result1 = $indent['openDel'] . $key . ": " . genStylishFormat($node['children'], $depth + 1);
            $result2 = $indent['openAdd'] . $key . ": " . showBoolValue($node['value']);
            $result = implode("\n", [$result1, $result2]);
        }
    } else {
        $result = $indent['standart'] . $key . ": " . genStylishFormat($node['children'], $depth + 1);
    }

    return $result;
}

function genValueToLine($key, $node, $indent)
{
    if ($node['meta'] === 'add') {
        $result = $indent['openAdd'] . $key . ": " . showBoolValue($node['value']);
    } elseif ($node['meta'] === 'deleted') {
        $result = $indent['openDel'] . $key . ": " . showBoolValue($node['value']);
    } elseif ($node['meta'] === 'newValue') {
        $result1 = $indent['openDel'] . $key . ": " . showBoolValue($node['oldValue']);
        $result2 = $indent['openAdd'] . $key . ": " . showBoolValue($node['value']);
        $result = implode("\n", [$result1, $result2]);
    } else {
        $result = $indent['standart'] . $key . ": " . showBoolValue($node['value']);
    }

    return $result;
}

function genIndent($depth)
{
    return [
        'standart' => times("    ", $depth),
        'close' => times("    ", $depth - 1),
        'openAdd' => times("    ", $depth - 1) . "  + ",
        'openDel' => times("    ", $depth - 1) . "  - "
    ];
}
