<?php

namespace FindDifferent\parser;

use Symfony\Component\Yaml\Yaml;

function getData(string $nameFile)
{
    if (getExpansion($nameFile) === "json") {
        $data = getDataJson($nameFile);
    } elseif (getExpansion($nameFile) === "yml") {
        $data = getDataYml($nameFile);
    } else {
        echo "PARSING ERROR!\n";
        return;
    }
    $result = removeBoolValues($data);
    return $result;
    // return $data;
}

function getDataJson(string $fileJson)
{
    $data = json_decode(file_get_contents($fileJson));
    // echo "!!!\n";
    // print_r(boo($data));
    // echo "!!!\n";
    return $data;
}

function getDataYml(string $fileYml)
{
    $data = Yaml::parseFile($fileYml, Yaml::PARSE_OBJECT_FOR_MAP);
    //print_r($fileYml);
    return $data;
}

function getExpansion(string $name)
{
    [, $expansion] = explode(".", $name);
    return $expansion;
}

function removeBoolValues(&$data)
{
    if (!is_object($data)) {
        return $data;
    }
    foreach ($data as $key => $value) {
        if ($data->$key === false) {
            $data->$key = 'false';
        };
        if ($data->$key === true) {
            $data->$key = 'true';
        };
        removeBoolValues($data->$key);
    }
    return $data;
}
