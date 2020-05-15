<?php

namespace FindDifferent\docopt;
use Docopt;

function runDocopt($doc)
{
    $pathLoc = __DIR__ . '/../vendor/docopt/docopt/src/docopt.php';
    $pathGlob = __DIR__ . '/../../../docopt/docopt/src/docopt.php';
    if (file_exists($pathGlob)) {
        require_once $pathGlob;
    } else {
        require_once $pathLoc;
    }
    // require('vendor/docopt/docopt/src/docopt.php');
    $args = Docopt::handle($doc, array('version'=>'Find different 0.1'));
    foreach ($args as $k=>$v)
        echo $k.': '.json_encode($v).PHP_EOL;
}
