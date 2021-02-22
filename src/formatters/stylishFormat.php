<?php

namespace GenerateDiff\formatters\stylishFormat;

use Exception;

use function Funct\Strings\times;
use function GenerateDiff\formatters\treeProcessing\isLeaf;

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

    switch ($node['meta']) {
        case 'notChangedValue':
            $result = $indent['standart'] . $key . ": " . $value2;
            break;
        case 'addedNode':
            $result = $indent['openAdd'] . $key . ": " . genLineValue($value2, $depth + 1);
            break;
        case 'removedNode':
            $result = $indent['openDel'] . $key . ": " . genLineValue($value1, $depth + 1);
            break;
        case 'changedValue':
            $result1 = $indent['openDel'] . $key . ": " . genLineValue($value1, $depth + 1);
            $result2 = $indent['openAdd'] . $key . ": " . genLineValue($value2, $depth + 1);
            $result = implode("\n", [$result1, $result2]);
            break;
        default:
            throw new Exception("\"{$node['meta']}\" is invalid meta");
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
        if (isLeaf($node)) {
            $acc[] = genLine($node, $key, $depth);
        } else {
            $acc[] = $indent['standart'] . $key . ": " . genStylishFormat($node['children'], $depth + 1);
        }
        return $acc;
    }, []);

    if (is_array($tree)) {
        $result = encloseInParentheses($result, $depth);
    }

    return implode("\n", $result);
}
