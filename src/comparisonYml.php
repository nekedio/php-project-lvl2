<?php

namespace FindDifferent\comparisonYml;

use Symfony\Component\Yaml\Yaml;

use function FindDifferent\comparison\getDiff;

function getDiffYml($firstFile, $secondFile)
{
    $firstYml = Yaml::parse(file_get_contents($firstFile));
    $secondYml = Yaml::parse(file_get_contents($secondFile));
    // print_r([$firstYml, $secondYml]);
    
    return getDiff($firstYml, $secondYml);
}
