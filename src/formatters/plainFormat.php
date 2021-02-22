<?php

namespace GenerateDiff\formatters\plainFormat;

use Exception;

use function Funct\Strings\times;
use function GenerateDiff\formatters\treeProcessing\isLeaf;

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
    $result = "'" . $boolValue . "'";
    return $result;
}

function getValue($value)
{
    if (is_object($value)) {
        return '[complex value]';
    }
    return boolToString($value);
}

function delVoidLine($events)
{
    return array_filter($events, function ($event) {
        return ($event != null);
    });
}

function getChanged($path, $node)
{
    $path = "'" . trim($path, ".") . "'";
    $value1 = getValue($node['value1']);
    $value2 = getValue($node['value2']);

    switch ($node['meta']) {
        case 'addedNode':
            $event = "Property " . $path . " was added with value: " . $value2;
            break;
        case 'removedNode':
            $event = "Property " . $path . " was removed";
            break;
        case 'changedValue':
            $event = "Property " . $path . " was updated. From " . $value1 . " to " . $value2;
            break;
        case 'notChangedValue':
        case null:
            $event = "";
            break;
        default:
            throw new Exception("\"{$node['meta']}\" is invalid meta");
    }
    return $event;
}

function genPlainFormat($tree, $path = "")
{
    $result = array_reduce(array_keys($tree), function ($acc, $key) use ($tree, $path) {
        $path = $path . "." . $key;
        $node = $tree[$key];
        $acc[] = getChanged($path, $node);

        if (!isLeaf($node)) {
            $acc[] = genPlainFormat($node['children'], $path);
        }
        return $acc;
    }, []);
    return implode("\n", delVoidLine($result));
}
