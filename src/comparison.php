<?php

namespace FindDifferent\comparison;

function getDiff($first, $second)
{
    $merge = array_merge($first, $second);
    $result = array_reduce(array_keys($merge), function ($acc, $key) use ($merge, $first, $second) {
        $valueMerge = $merge[$key];
        $valueFirst = $first[$key] ?? null;
        
        if (array_key_exists($key, $first) && in_array($valueMerge, $first)) {
            if (array_key_exists($key, $second)) {
                $acc[] = "  {$key}: {$valueMerge}";
            } else {
                $acc[] = "- {$key}: {$valueMerge}";
            }
        } elseif (array_key_exists($key, $first) && $valueMerge !== $valueFirst) {
            $acc[] = "+ {$key}: {$valueMerge}\n- {$key}: {$valueFirst}";
        } elseif ($valueFirst === null) {
            $acc[] = "+ {$key}: {$valueMerge}";
        }

        return $acc;
    }, []);
    $result = implode("\n", $result);
    return "{\n{$result}\n}\n";
}
