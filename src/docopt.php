<?php

namespace FindDifferent\docopt;

use Docopt;

function runDocopt($doc)
{
    $args = Docopt::handle($doc, array('version' => 'Find different 0.1'));
    // foreach ($args as $k => $v) {
    //     // echo $k . ': ' . json_encode($v) . PHP_EOL;
    // }
    $format = $args['--format'];
    $firstFile = $args['<firstFile>'];
    $secondFile = $args['<secondFile>'];
    echo "{$format} : {$firstFile} {$secondFile}\n";
}
