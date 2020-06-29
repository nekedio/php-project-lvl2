<?php

namespace FindDifferent\comparison;

use function FindDifferent\parsers\getDataFromJson;
use function FindDifferent\parsers\getDataFromYaml;

function getDiff($format, $first, $second)
{
    if ($format === 'json' || $format === 'pretty') {
        $dataFirst = getDataFromJson($first);
        $dataSecond = getDataFromJson($second);
    } elseif ($format === 'yml') {
        $dataFirst = getDataFromYaml($first);
        $dataSecond = getDataFromYaml($second);
    }

    $first = get_object_vars($dataFirst);
    $second = get_object_vars($dataSecond);

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

    // print_r([$format, $dataFirst, $dataSecond]);
}
