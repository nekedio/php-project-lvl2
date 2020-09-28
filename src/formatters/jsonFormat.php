<?php

namespace FindDifferent\formatters\jsonFormat;

function getDiffJson($diff)
{
    return json_encode(func($diff));
}

function func($diff)
{
    $result = array_reduce(array_keys($diff), function ($acc, $key) use ($diff) {
        if ($diff[$key]['meta'] === 'deleted') {
            $acc[$key] = 'deletedKey';
        } elseif ($diff[$key]['meta'] === 'add') {
            $acc[$key] = 'addedKey';
        } elseif ($diff[$key]['meta'] === 'newValue') {
            if ($diff[$key]['value'] == null) {
                $acc[$key] = 'addedChildren';
            } elseif ($diff[$key]['oldValue'] == null) {
                $acc[$key] = 'deletedChildren';
            } else {
                $acc[$key] = 'changedValue';
            }
        } else {
            if ($diff[$key]['children'] === []) {
                if ($diff[$key]['meta'] === 'newValue') {
                    $acc[$key] = 'changedValue';
                } else {
                    $acc[$key] = 'noChange';
                }
            } else {
                $acc[$key] = func($diff[$key]['children']);
            }
        }
        return $acc;
    }, []);
    return $result;
}
