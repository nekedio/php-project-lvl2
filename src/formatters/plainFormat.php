<?php

namespace FindDifferent\formatters\plainFormat;

use function Funct\Strings\times;
use function FindDifferent\formatters\additionalFunc\showBoolValue;

function genPlainFormat($tree, $path = "")
{
    $result = array_reduce(array_keys($tree), function ($acc, $key) use ($tree, $path) {
        $path = $path . "." . $key;
        $acc[] = getChanged(
            $path,
            $tree[$key]
        );
        if ($tree[$key]['children'] != []) {
            $acc[] = genPlainFormat($tree[$key]['children'], $path);
        }
        return $acc;
    }, []);
    return implode("\n", delVoidLine($result));
}

function getChanged($path, $node)
{
    $oldValue = $node['oldValue'] ?? null;
    if ($node['value'] === null) {
        $value = '[complex value]';
    } elseif ($node['value'] === false || $node['value'] === true) {
        $value = showBoolValue($node['value']);
    } else {
        $value = "'" . $node['value'] . "'";
    }
    if ($oldValue === null) {
        $oldValue = '[complex value]';
    } elseif ($oldValue === false || $oldValue === true) {
        $oldValue = showBoolValue($oldValue);
    } else {
        $oldValue = "'" . $oldValue . "'";
    }
    $path = "'" . trim($path, ".") . "'";
    if ($node['meta']  === 'add') {
        $event = "Property " . $path . " was added with value: " . $value;
    } elseif ($node['meta'] === 'deleted') {
        $event = "Property " . $path . " was removed";
    } elseif ($node['meta'] === 'newValue' || $node['meta'] === 'oldValue') {
        if ($node['meta'] === 'newValue') {
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
