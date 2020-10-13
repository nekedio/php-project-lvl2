<?php

namespace CompareTool\parser;

use Symfony\Component\Yaml\Yaml;
use Exception;

function parse(string $content, string $extension)
{
    switch ($extension) {
        case 'json':
            $data = parseJson($content);
            break;
        case 'yml':
            $data = parseYaml($content);
            break;
        default:
            throw new Exception("Incorrect extension file '.$extension'");
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
