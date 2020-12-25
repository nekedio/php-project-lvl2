<?php

namespace CompareTool\formatters\stylishFormat;

use function Funct\Strings\times;

function genStylishFormat($tree, $depth = 1)
{
    $indent = genIndent($depth);
    $result = array_reduce(array_keys($tree), function ($acc, $key) use ($tree, $indent, $depth) {
        $node = $tree[$key];
        if ($tree[$key]['children'] === []) {
            $acc[] = genLine($tree, $key, $depth);
        } else {
            $acc[] = $indent['standart'] . $key . ": " . genStylishFormat($tree[$key]['children'], $depth + 1);
        }
        return $acc;
    }, []);

    if (is_array($tree)) {
        $result = encloseInParentheses($result, $depth);
    }

    return implode("\n", $result);
}

function genLine($tree, $key, $depth)
{
    $value1 = showBoolValueStylishFormat($tree[$key]['value1']);
    $value2 = showBoolValueStylishFormat($tree[$key]['value2']);
    $indent = genIndent($depth);
    if ($value1 === $value2) {
        $result = $indent['standart'] . $key . ": " . $value2;
        return $result;
    }
    if ($tree[$key]['meta'] == 'addNode') {
        $result = $indent['openAdd'] . $key . ": " . genLineValue($value2, $depth + 1);
        return $result;
    }
    if ($tree[$key]['meta'] == 'deletedNode') {
        $result = $indent['openDel'] . $key . ": " . genLineValue($value1, $depth + 1);
        return $result;
    }
    if (($value1 !== $value2)) {
        $result1 = $indent['openDel'] . $key . ": " . genLineValue($value1, $depth + 1);
        $result2 = $indent['openAdd'] . $key . ": " . genLineValue($value2, $depth + 1);
        $result = implode("\n", [$result1, $result2]);
        return $result;
    }
}

function genLineValue($value, $depth)
{
    if (is_object($value)) {
        $node = get_object_vars($value);
        $result = array_reduce(array_keys($node), function ($acc, $key) use ($node, $depth) {
            if (is_object($node[$key])) {
                $acc[] = times("    ", $depth) . $key . ": " . genLineValue($node[$key], $depth + 1);
            } else {
                $acc[] = times("    ", $depth) . $key . ": " . $node[$key];
            }
            return $acc;
        }, []);
        
        if (is_array($node)) {
            $result = encloseInParentheses($result, $depth);
        }

        return implode("\n", $result);
    }
    return $value;
}

function encloseInParentheses($node, $depth)
{
    array_unshift($node, "{");
    $node[] = times("    ", $depth - 1) . "}";
    return $node;
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

function showBoolValueStylishFormat($boolValue)
{
    if ($boolValue === true) {
        return 'true';
    }
    if ($boolValue === false) {
        return 'false';
    }
    if ($boolValue === null) {
        return 'null';
    }
    return $boolValue;
}
