<?php

namespace FindDifferent\formatters\jsonFormat;

function genJsonFormat($tree)
{
        return json_encode(traversalDiff($tree));
}

function traversalDiff($tree)
{
    $result = array_reduce(array_keys($tree), function ($acc, $key) use ($tree) {
        if ($tree[$key]['meta'] === 'deleted') {
            $acc[$key] = 'deletedKey';
        } elseif ($tree[$key]['meta'] === 'add') {
            $acc[$key] = 'addedKey';
        } elseif ($tree[$key]['meta'] === 'newValue') {
            if ($tree[$key]['value'] == null) {
                $acc[$key] = 'addedChildren';
            } elseif ($tree[$key]['oldValue'] == null) {
                $acc[$key] = 'deletedChildren';
            } else {
                $acc[$key] = 'changedValue';
            }
        } else {
            if ($tree[$key]['children'] === []) {
                if ($tree[$key]['meta'] === 'newValue') {
                    $acc[$key] = 'changedValue';
                } else {
                    $acc[$key] = 'noChange';
                }
            } else {
                $acc[$key] = traversalDiff($tree[$key]['children']);
            }
        }
        return $acc;
    }, []);
    return $result;
}
