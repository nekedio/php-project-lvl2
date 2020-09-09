<?php

namespace FindDifferent\parser;

use Symfony\Component\Yaml\Yaml;

function getData(string $nameFile)
{
    if (getExpansion($nameFile) === "json") {
        return getDataJson($nameFile);
    } elseif (getExpansion($nameFile) === "yml") {
        return getDataYml($nameFile);
    } else {
        echo "PARSING ERROR!\n";
        return;
    }
}

function getDataJson(string $fileJson)
{
    $data = json_decode(file_get_contents($fileJson));
    //echo "!!!\n";
    //print_r($data);
    //echo "!!!\n";
    return $data;
}

function getDataYml(string $fileYml)
{
    $data = Yaml::parseFile($fileYml, Yaml::PARSE_OBJECT_FOR_MAP);
    return $data;
}

function getExpansion(string $name)
{
    [, $expansion] = explode(".", $name);
    return $expansion;
}
