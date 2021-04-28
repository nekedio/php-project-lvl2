<?php

namespace GenerateDiff\formatters\jsonFormat;

function genJsonFormat(array $tree): string
{
    return (string) json_encode($tree);
}
