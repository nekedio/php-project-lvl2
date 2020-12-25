<?php

namespace CompareTool\formatters\additionalFunc;

function showBoolValue($boolValue)
{
    if ($boolValue === true) {
        return 'true';
    }
    if ($boolValue === false) {
        return 'false';
    }
    if ($boolValue === null){
        return 'null';
    }
    return $boolValue;
}
