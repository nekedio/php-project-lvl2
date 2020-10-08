<?php

namespace FindDifferent\parser;

use Symfony\Component\Yaml\Yaml;

function parse(string $link)
{
    $expansion = getExpansion($link);
    $contents = file_get_contents($link);
    switch ($expansion) {
        case 'json':
            $data = parseJson($contents);
            break;
        case 'yml':
            $data = parseYaml($contents);
            break;
        default:
            $error = "ERROR!\n" . "gendiff: incorrect expansion file \"." . $expansion . "\"\n";
            echo $error;
    }
    return $data;
}

function parseJson(string $jsonContents)
{
    $data = json_decode($jsonContents);
    return $data;
}

function parseYaml(string $yamlContents)
{
    $data = Yaml::parse($yamlContents, Yaml::PARSE_OBJECT_FOR_MAP);
    return $data;
}

function getExpansion(string $link)
{
    [, $expansion] = explode(".", $link);
    return $expansion;
}
