<?php

namespace FindDifferent\formatters\plainFormat;

use function Funct\Strings\times;

function getDiffPlain($diff)
{
    $diffString = func($diff);
    return $diffString;
}

function func($diff, $path = "")
{
    $result = array_reduce(array_keys($diff), function ($acc, $key) use ($diff, $path) {
        $path = $path . "." . $diff[$key]['name'];
        $acc[] = event(
            $path,
            $diff[$key]['meta'],
            $diff[$key]['value'],
            $diff[$key]['children'],
            $diff[$key]['oldValue'] ?? null
        );
        if ($diff[$key]['children'] != []) {
            $acc[] = func($diff[$key]['children'], $path);
        }
        return $acc;
    }, []);
    return implode("\n", filter($result));
}

function event($path, $meta, $value, $children, $oldValue)
{
    if ($value === null) {
        $value = '[complex value]';
    } elseif ($value !== 'false' && $value !== 'true') {
        $value = "'" . $value . "'";
    }
    if ($oldValue === null) {
        $oldValue = '[complex value]';
    } elseif ($oldValue !== 'false' && $oldValue !== 'true') {
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

function filter($events)
{
    return array_filter($events, function ($event) {
        return ($event != null);
    });
}
