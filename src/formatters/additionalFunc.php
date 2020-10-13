<?php

namespace CompareTool\formatters\additionalFunc;

function showBoolValue($boolValue)
{
    if ($boolValue === true) {
        $value = 'true';
    } elseif ($boolValue === false) {
        $value = 'false';
    } else {
        $value = $boolValue;
    }
    return $value;
}
