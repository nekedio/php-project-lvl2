<?php

namespace GenerateDiff\formatters\stylishFormat;

use Exception;

use function Funct\Strings\times;

function genIndent($depth)
{
    return [
        'standart' => times("    ", $depth),
        'close' => times("    ", $depth - 1),
        'openAdd' => times("    ", $depth - 1) . "  + ",
        'openDel' => times("    ", $depth - 1) . "  - "
    ];
}

function boolToString($boolValue)
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

function genLine($node, $key, $depth)
{
    $value1 = boolToString($node['value1']);
    $value2 = boolToString($node['value2']);
    $indent = genIndent($depth);

    switch ($node['type']) {
        case 'notChangedValue':
            $result = $indent['standart'] . $node['name'] . ": " . $value2;
            break;
        case 'addedLeaf':
            $result = $indent['openAdd'] . $node['name'] . ": " . genLineValue($value2, $depth + 1);
            break;
        case 'removedLeaf':
            $result = $indent['openDel'] . $node['name'] . ": " . genLineValue($value1, $depth + 1);
            break;
        case 'changedValue':
            $result1 = $indent['openDel'] . $node['name'] . ": " . genLineValue($value1, $depth + 1);
            $result2 = $indent['openAdd'] . $node['name'] . ": " . genLineValue($value2, $depth + 1);
            $result = implode("\n", [$result1, $result2]);
            break;
        default:
            throw new Exception("\"{$node['type']}\" is invalid type");
    }
    return $result;
}

function encloseInParentheses($node, $depth)
{
    array_unshift($node, "{");
    $node[] = times("    ", $depth - 1) . "}";
    return $node;
}

function genStylishFormat($tree, $depth = 1)
{
    $indent = genIndent($depth);
    $result = array_reduce(array_keys($tree), function ($acc, $key) use ($tree, $indent, $depth) {
        $node = $tree[$key];
        if ($node['type'] != 'node') {
            $acc[] = genLine($node, $key, $depth);
        } else {
            //print_r($node);
            $acc[] = $indent['standart'] . $node['name'] . ": " . genStylishFormat($node['children'], $depth + 1);
        }
        return $acc;
    }, []);

    if (is_array($tree)) {
        $result = encloseInParentheses($result, $depth);
    }

    return implode("\n", $result);
}
