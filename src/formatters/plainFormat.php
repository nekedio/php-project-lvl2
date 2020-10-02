<?php

namespace FindDifferent\formatters\plainFormat;

use function Funct\Strings\times;
use function FindDifferent\formatters\additionalFunc\showBoolValue;

function genPlainFormat($tree, $path = "")
{
    $result = array_reduce(array_keys($tree), function ($acc, $key) use ($tree, $path) {
        $path = $path . "." . $key;
        $acc[] = event(
            $path,
            $tree[$key]['meta'],
            $tree[$key]['value'],
            $tree[$key]['children'],
            $tree[$key]['oldValue'] ?? null
        );
        if ($tree[$key]['children'] != []) {
            $acc[] = genPlainFormat($tree[$key]['children'], $path);
        }
        return $acc;
    }, []);
    return implode("\n", delVoidLine($result));
}

function event($path, $meta, $value, $children, $oldValue)
{
    if ($value === null) {
        $value = '[complex value]';
    } elseif ($value === false || $value === true) {
        $value = showBoolValue($value);
    } else {
        $value = "'" . $value . "'";
    }
    if ($oldValue === null) {
        $oldValue = '[complex value]';
    } elseif ($oldValue === false || $oldValue === true) {
        $oldValue = showBoolValue($oldValue);
    } else {
        $oldValue = "'" . $oldValue . "'";
    }
    $path = "'" . trim($path, ".") . "'";
    if ($meta  === 'add') {
        $event = "Property " . $path . " was added with value: " . $value;
    } elseif ($meta === 'deleted') {
        $event = "Property " . $path . " was removed";
    } elseif ($meta === 'newValue' || $meta === 'oldValue') {
        if ($meta === 'newValue') {
            $event = "Property " . $path . " was updated. From " . $oldValue . " to " . $value;
        } else {
            $event = null;
        }
    } else {
        $event = null;
    }

    return $event;
}

function delVoidLine($events)
{
    return array_filter($events, function ($event) {
        return ($event != null);
    });
}
