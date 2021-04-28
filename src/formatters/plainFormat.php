<?php

namespace GenerateDiff\formatters\plainFormat;

use Exception;

use function Funct\Strings\times;

/**
 * Functiondescription
 * @autor nekedio
 * @param mixed $value
 **/
function toString($value): string
{
    if (is_object($value)) {
        return '[complex value]';
    }
    return ($value === null) ? 'null' : var_export($value, true);
}

function genPlainFormat(array $tree, string $path = ""): string
{
    $lines = array_map(function ($key) use ($tree, $path) {
        $node = $tree[$key];
        $path = trim("{$path}.{$node['name']}", ".");
        $value1 = toString($node['value1']);
        $value2 = toString($node['value2']);

        switch ($node['type']) {
            case 'node':
                return genPlainFormat($node['children'], $path);
            case 'added':
                $line = "Property '{$path}' was added with value: {$value2}";
                break;
            case 'removed':
                $line = "Property '{$path}' was removed";
                break;
            case 'changedValue':
                $line = "Property '{$path}' was updated. From {$value1} to {$value2}";
                break;
            case 'notChangedValue':
                $line = "";
                break;
            default:
                throw new Exception("\"{$node['type']}\" is invalid type");
        }
        return $line;
    }, array_keys($tree));
    $result = array_filter($lines, fn($line) => $line != null);
    return implode("\n", $result);
}
