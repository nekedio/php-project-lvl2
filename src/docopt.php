<?php

namespace FindDifferent\docopt;

use Docopt;
use Funct\Collection;

use function FindDifferent\comparison\getDiffJson;

function runDocopt($doc)
{
    $args = Docopt::handle($doc, array('version' => 'Find different 0.1'));
    $format = Collection\get($args, '--format');
    $firstFile = Collection\get($args, '<firstFile>');
    $secondFile = Collection\get($args, '<secondFile>');
    $diff = getDiffJson($firstFile, $secondFile);
    echo $diff;
    return;
}
