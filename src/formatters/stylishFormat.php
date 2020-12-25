<?php

namespace CompareTool\formatters\stylishFormat;

use function Funct\Strings\times;
use function CompareTool\formatters\additionalFunc\showBoolValue;

function genStylishFormat($tree, $depth = 1)
{
    $indent = genIndent($depth);
    $result = array_reduce(array_keys($tree), function ($acc, $key) use ($tree, $indent, $depth) {
        $node = $tree[$key];
        if ($tree[$key]['children'] === []) {
            $acc[] = genLine($tree, $key, $depth);
        } else {
            $acc[] = $indent['standart'] . $key. ": " . genStylishFormat($tree[$key]['children'], $depth + 1);
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
    $value1 = showBoolValue($tree[$key]['value1']);
    $value2 = showBoolValue($tree[$key]['value2']);
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
    if (is_object($value1)) {
        
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





/*
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
    if ($node['meta'] === 'addNode') {
        $result = $indent['openAdd'] . $key . ": " . genStylishFormat($node['children'], $depth + 1);
    } elseif ($node['meta'] === 'deletedNode') {
        $result = $indent['openDel'] . $key . ": " . genStylishFormat($node['children'], $depth + 1);
    // } elseif ($node['value1'] != $node['value2']) {
    //         $result1 = $indent['openDel'] . $key . ": " . showBoolValue($node['value1']);
    //         $result2 = $indent['openAdd'] . $key . ": " . showBoolValue($node['value2']);
    //         $result = implode("\n", [$result1, $result2]);
    } else {
        $result = $indent['standart'] . $key . ": " . genStylishFormat($node['children'], $depth + 1);
    }

    return $result;
}

// function genKeyToLine($key, $node, $indent, $depth)
// {
//     if ($node['meta'] === 'add') {
//         $result = $indent['openAdd'] . $key . ": " . genStylishFormat($node['children'], $depth + 1);
//     } elseif ($node['meta'] === 'deleted') {
//         $result = $indent['openDel'] . $key . ": " . genStylishFormat($node['children'], $depth + 1);
//     } elseif ($node['meta'] === 'newValue') {
//         if ($node['value'] === null) {
//             $result1 = $indent['openDel'] . $key . ": " . showBoolValue($node['oldValue']);
//             $result2 = $indent['openAdd'] . $key . ": " . genStylishFormat($node['children'], $depth + 1);
//             $result = implode("\n", [$result1, $result2]);
//         } else {
//             $result1 = $indent['openDel'] . $key . ": " . genStylishFormat($node['children'], $depth + 1);
//             $result2 = $indent['openAdd'] . $key . ": " . showBoolValue($node['value']);
//             $result = implode("\n", [$result1, $result2]);
//         }
//     } else {
//         $result = $indent['standart'] . $key . ": " . genStylishFormat($node['children'], $depth + 1);
//     }
//
//     return $result;
// }

function genValueToLine($key, $node, $indent)
{
    // if ($node['meta'] === 'addNode') {
    //     $result = $indent['openAdd'] . $key . ": " . showBoolValue($node['value2']);
    // } elseif ($node['meta'] === 'deletedNode') {
    //     $result = $indent['openDel'] . $key . ": " . showBoolValue($node['value2']);
    // } else
    if ($node['value1'] !== $node['value2']) {
        $result1 = $indent['openDel'] . $key . ": " . showBoolValue($node['value1']);
        $result2 = $indent['openAdd'] . $key . ": " . showBoolValue($node['value2']);
        $result = implode("\n", [$result1, $result2]);
    } else {
        $result = $indent['standart'] . $key . ": " . showBoolValue($node['value2']);
    }

    return $result;
}

// function genValueToLine($key, $node, $indent)
// {
//     if ($node['meta'] === 'add') {
//         $result = $indent['openAdd'] . $key . ": " . showBoolValue($node['value']);
//     } elseif ($node['meta'] === 'deleted') {
//         $result = $indent['openDel'] . $key . ": " . showBoolValue($node['value']);
//     } elseif ($node['meta'] === 'newValue') {
//         $result1 = $indent['openDel'] . $key . ": " . showBoolValue($node['oldValue']);
//         $result2 = $indent['openAdd'] . $key . ": " . showBoolValue($node['value']);
//         $result = implode("\n", [$result1, $result2]);
//     } else {
//         $result = $indent['standart'] . $key . ": " . showBoolValue($node['value']);
//     }
//
//     return $result;
// }

function genIndent($depth)
{
    return [
        'standart' => times("    ", $depth),
        'close' => times("    ", $depth - 1),
        'openAdd' => times("    ", $depth - 1) . "  + ",
        'openDel' => times("    ", $depth - 1) . "  - "
    ];
}
 */
