<?php

namespace FindDifferent\formatters\formatYaml;

use function Funct\Strings\times;

function getDiffYaml($diff)
{
    $diffString = function ($diff, $depth = 1) use (&$diffString) {
        $result = array_reduce(array_keys($diff), function ($acc, $key) use ($diff, $depth, $diffString) {
            $indent = getIndent($diff[$key]['meta'], $depth);
            if ($diff[$key]['value'] === null) {
                $acc[] = $indent['open'] . $diff[$key]['name'] . ": ";
            } else {
                $acc[] = $indent['open'] . $diff[$key]['name'] . ": " . $diff[$key]['value'];
            }
            if ($diff[$key]['children'] != []) {
                $acc[] = $diffString($diff[$key]['children'], $depth + 1);
            }
            return $acc;
        }, []);
        return implode("\n", $result);
    };
    $result = "---\n" . $diffString($diff) . "\n";
    return $result;
}

function getIndent($meta, $depth)
{
    if ($meta === 'add' || $meta === 'newValue') {
        $indentOpen =  times("  ", $depth - 1) . "+ ";
    } elseif ($meta === 'deleted' || $meta === 'oldValue') {
         $indentOpen = times("  ", $depth - 1) . "- ";
    } else {
        $indentOpen = times("  ", $depth);
    }
    $result = [
        'open' => $indentOpen,
        'close' => times("  ", $depth)
    ];
    return $result;
}
