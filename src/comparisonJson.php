<?php

namespace FindDifferent\comparisonJson;

use function FindDifferent\comparison\getDiff;

function getDiffJson($firstFile, $secondFile)
{
    $firstJson = json_decode(file_get_contents($firstFile), true);
    $secondJson = json_decode(file_get_contents($secondFile), true);
    // print_r([$firstJson, $secondJson]);
    return getDiff($firstJson, $secondJson);
}