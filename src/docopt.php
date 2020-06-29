<?php

namespace FindDifferent\docopt;

use Docopt;
use Funct\Collection;

// use function FindDifferent\comparisonJson\getDiffJson;
// use function FindDifferent\comparisonYml\getDiffYml;

use function FindDifferent\comparison\getDiff;

function runDocopt()
{
    $doc = <<<DOC
    Generate diff

    Usage:
      gendiff (-h|--help)
      gendiff (-v|--version)
      gendiff [--format <fmt>] <firstFile> <secondFile>

    Options:
      -h --help                     Show this screen.
      -v --version                  Show version.
      --format <fmt>                Report format [default: pretty]
    DOC;

    $args = Docopt::handle($doc, array('version' => 'Find different 0.1'));
    $format = Collection\get($args, '--format');
    $firstFile = Collection\get($args, '<firstFile>');
    $secondFile = Collection\get($args, '<secondFile>');

    $diff = getDiff($format, $firstFile, $secondFile);

    echo $diff;
    return;
}
