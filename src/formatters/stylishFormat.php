<?php

namespace GenerateDiff\formatters\stylishFormat;

use Exception;

use function Funct\Strings\times;

function genIndents(int $depth): array
{
    return [
        'notChanged' => times("    ", $depth),
        'closingBracket' => times("    ", $depth - 1),
        'added' => times("    ", $depth - 1) . "  + ",
        'removed' => times("    ", $depth - 1) . "  - "
    ];
}

/**
 * Functiondescription
 * @autor nekedio
 * @param mixed $currentValue
 **/
function toString($currentValue, int $depth = 1): string
{
    $indents = genIndents($depth);
    if (!is_object($currentValue)) {
        return ($currentValue === null) ? 'null' : trim(var_export($currentValue, true), '\'');
    }
    $value = get_object_vars($currentValue);
    $lines = array_map(
        fn($key, $val) => implode([$indents['notChanged'], $key, ": ", toString($val, $depth + 1)]),
        array_keys($value),
        $value
    );

    $result = ["{", ...$lines, "{$indents['closingBracket']}}"];
    return implode("\n", $result);
}

function genStylishFormat(array $tree, int $depth = 1): string
{
    $indents = genIndents($depth);
    $lines = array_map(function ($key) use ($tree, $depth, $indents) {
        $node = $tree[$key];
        switch ($node['type']) {
            case 'node':
                $line = implode([
                    $indents['notChanged'],
                    $node['name'], ": ",
                    genStylishFormat($node['children'], $depth + 1)
                ]);
                break;
            case 'notChangedValue':
                $value2 = toString($node['value2'], $depth + 1);
                $line = implode([$indents['notChanged'], $node['name'], ": ", $value2]);
                break;
            case 'added':
                $value2 = toString($node['value2'], $depth + 1);
                $line = implode([$indents['added'], $node['name'], ": ", $value2]);
                break;
            case 'removed':
                $value1 = toString($node['value1'], $depth + 1);
                $line = implode([$indents['removed'], $node['name'], ": ", $value1]);
                break;
            case 'changedValue':
                $value1 = toString($node['value1'], $depth + 1);
                $value2 = toString($node['value2'], $depth + 1);
                $line1 = implode([$indents['removed'], $node['name'] . ": " . $value1]);
                $line2 = implode([$indents['added'], $node['name'] . ": " . $value2]);
                $line = implode("\n", [$line1, $line2]);
                break;
            default:
                throw new Exception("\"{$tree['type']}\" is invalid type");
        }
        return $line;
    }, array_keys($tree));
    $result = ["{", ...$lines, "{$indents['closingBracket']}}"];
    return implode("\n", $result);
}
