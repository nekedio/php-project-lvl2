<?php

namespace CompareTool\formatters\plainFormat;

use function Funct\Strings\times;

function genPlainFormat($tree, $path = "")
{
    $result = array_reduce(array_keys($tree), function ($acc, $key) use ($tree, $path) {
        $path = $path . "." . $key;
        $acc[] = getChanged($path, $tree[$key]);

        if ($tree[$key]['children'] != []) {
            $acc[] = genPlainFormat($tree[$key]['children'], $path);
        }
        return $acc;
    }, []);
    return implode("\n", delVoidLine($result));
}

function getChanged($path, $node)
{
    $path = "'" . trim($path, ".") . "'";
    $value1 = getValue($node['value1']);
    $value2 = getValue($node['value2']);
    
    if ($node['meta'] == 'addNode') {
        $event = "Property " . $path . " was added with value: " . $value2;
        return $event;
    }
    if ($node['meta'] == 'deletedNode') {
        $event = "Property " . $path . " was removed";
        return $event;
    }
    if ($value1 !== $value2) {
        $event = "Property " . $path . " was updated. From " . $value1 . " to " . $value2;
        return $event;
    }
    return;
}

function getValue($value)
{
    if (is_object($value)) {
        return '[complex value]';
    }
    return showBoolValuePlainFormat($value);
}

function delVoidLine($events)
{
    return array_filter($events, function ($event) {
        return ($event != null);
    });
}

function showBoolValuePlainFormat($boolValue)
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
