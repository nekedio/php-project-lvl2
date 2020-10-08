<?php

namespace FindDifferent\docopt;

use Docopt;
use Funct\Collection;

use function FindDifferent\comparison\genOutput;

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
      --format <fmt>                Report format [default: stylish]
    DOC;

    $args = Docopt::handle($doc, array('version' => 'Find different 0.11'));
    
    $output = genOutput($args['<firstFile>'], $args['<secondFile>'], $args['--format']);
    echo $output . "\n";
}
