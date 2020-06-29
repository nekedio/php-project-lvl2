<?php

namespace FindDifferent\parsers;

use Symfony\Component\Yaml\Yaml;

function getDataFromJson($fileJson)
{
    $data = json_decode(file_get_contents($fileJson));
    return $data;
}

function getDataFromYaml($fileYml)
{
    $data = Yaml::parseFile($fileYml, Yaml::PARSE_OBJECT_FOR_MAP);
    return $data;
}
