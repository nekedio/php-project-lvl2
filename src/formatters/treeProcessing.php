<?php

namespace GenerateDiff\formatters\treeProcessing;

function isLeaf($node)
{
    if ($node['children'] == []) {
        return true;
    }
    return false;
}
