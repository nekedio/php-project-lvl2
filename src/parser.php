<?php

namespace GenerateDiff\parser;

use Symfony\Component\Yaml\Yaml;
use Exception;

function parse(string $content, string $extension)
{
    switch ($extension) {
        case 'json':
            $data = json_decode($content);
            break;
        case 'yml':
            $data = Yaml::parse($content, Yaml::PARSE_OBJECT_FOR_MAP);
            break;
        default:
            throw new Exception("Incorrect extension file '.$extension'");
    }
    return $data;
}
