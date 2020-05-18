<?php

namespace FindDifferent\comparison;

use function Funct\Collection\union;
use function Funct\Collection\merge;

function getDiffJson($firstFile, $secondFile)
{
    $firstJson = json_decode(file_get_contents($firstFile), true);
    $secondJson = json_decode(file_get_contents($secondFile), true);
    $merge = array_merge($firstJson, $secondJson);
    $result = array_reduce(array_keys($merge), function ($acc, $key) use ($merge, $firstJson, $secondJson) {
        $valueMerge = $merge[$key];
        $valueFirstJson = $firstJson[$key] ?? null;
        
        if (array_key_exists($key, $firstJson) && in_array($valueMerge, $firstJson)) {
            if (array_key_exists($key, $secondJson)) {
                $acc[] = "  {$key}: {$valueMerge}";
            } else {
                $acc[] = "- {$key}: {$valueMerge}";
            }
        } elseif (array_key_exists($key, $firstJson) && $valueMerge !== $valueFirstJson) {
            $acc[] = "+ {$key}: {$valueMerge}\n- {$key}: {$valueFirstJson}";
        } elseif ($valueFirstJson === null) {
            $acc[] = "+ {$key}: {$valueMerge}";
        }

        return $acc;
    }, []);
    $result = implode("\n", $result);
    return "{\n{$result}\n}\n";
}
