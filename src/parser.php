<?php

namespace FindDifferent\parser;

use Symfony\Component\Yaml\Yaml;

function parse(string $pathToFile)
{
    $expansion = getExpansion($pathToFile);
    switch ($expansion) {
        case 'json':
            $data = parseJson($pathToFile);
            break;
        case 'yml':
            $data = parseYaml($pathToFile);
            break;
        default:
            $error = "ERROR!\n" . "gendiff: incorrect expansion file \"." . $expansion . "\"\n";
            echo $error;
    }
    
    return $data;
}

function parseJson(string $jsonFile)
{
    $data = json_decode(file_get_contents($jsonFile));
    return $data;
}

function parseYaml(string $yamlFile)
{
    $data = Yaml::parseFile($yamlFile, Yaml::PARSE_OBJECT_FOR_MAP);
    return $data;
}

function getExpansion(string $nameFile)
{
    [, $expansion] = explode(".", $nameFile);
    return $expansion;
}
