<?php

namespace FindDifferent\docopt;
use Docopt;

function runDocopt($doc)
{
    require('vendor/docopt/docopt/src/docopt.php');
    $args = Docopt::handle($doc, array('version'=>'Find different 0.1'));
    foreach ($args as $k=>$v)
        echo $k.': '.json_encode($v).PHP_EOL;
}
