<?php

namespace CompareTool\comparison;

use Exception;

use function CompareTool\parser\parse;
use function CompareTool\formatters\jsonFormat\genJsonFormat;
use function CompareTool\formatters\plainFormat\genPlainFormat;
use function CompareTool\formatters\stylishFormat\genStylishFormat;

function genOutput($pathToFile1, $pathToFile2, $outputFormat)
{
    $dataOfFile1 = parse(file_get_contents($pathToFile1), getExtension($pathToFile1));
    $dataOfFile2 = parse(file_get_contents($pathToFile2), getExtension($pathToFile2));
    
    //$tree1 = genTree($dataOfFile1);
    //$tree2 = genTree($dataOfFile2);

    //$diff = genDiff($tree1, $tree2);

    $diff = genDiff($dataOfFile1, $dataOfFile2);
    
    //print_r($diff);
    
    $sortDiff = sortTree($diff); //sortTree($diff);

    print_r($sortDiff);

    // switch ($outputFormat) {
    //     case 'json':
    //         $output = genJsonFormat($sortDiff);
    //         break;
    //     case 'plain':
    //         $output = genPlainFormat($sortDiff);
    //         break;
    //     case 'stylish':
    //     case null:
    //         $output = genStylishFormat($sortDiff);
    //         break;
    //     default:
    //         throw new Exception("Unknown format '$outputFormat'");
    // }
    
    
    //$output = genJsonFormat($sortDiff);
    $output = genStylishFormat($sortDiff);
    //$output = genPlainFormat($sortDiff);
    return $output;
}

function getExtension(string $pathToFile)
{
    [, $extension] = explode(".", $pathToFile);
    return $extension;
}

// function genTree($object)
// {
//     $data = get_object_vars($object);
//     $tree = array_reduce(array_keys($data), function ($acc, $key) use ($data) {
//         if (is_object($data[$key])) {
//             $children = genTree($data[$key]);
//             $value = null;
//         } else {
//             $children = [];
//             $value = $data[$key];
//         }
//
//         $acc[$key] = [
//             'value' => $value,
//             'children' => $children
//         ];
//         return $acc;
//     }, []);
//     return $tree;
// }



function genDiff($objectData1, $objectData2)
{
    $data1 = get_object_vars($objectData1);
    $data2 = get_object_vars($objectData2);
    $nodeMerge = array_merge($data1, $data2); 
    $result = array_reduce(array_keys($nodeMerge), function ($acc, $key) use ($data1, $data2) {
        $children = getChildren($data1, $data2, $key);
        if ($children === []) {
            $acc[$key] = [
                //'name' => $key, 
                'value1' => $data1[$key] ?? null, 
                'value2' => $data2[$key] ?? null,
                'meta' => genMeta($data1, $data2, $key, $children),
                //'meta2' => genMeta2($data1, $data2, $key),
                'children' => $children,
            ];
        } else {
            $acc[$key] = [
                //'name' => $key,
                'value1' => null, //genValue1($data1, $data2, $key, $children),
                'value2' => null, //genValue2($data2, $data1, $key, $children),
                'meta' => genMeta($data1, $data2, $key, $children),
                //'meta2' => genMeta2($data1, $data2, $key),
                'children' => $children,
            ];
        }
        return $acc;
    }, []);
    return  $result;
}

// function genValue1($data1, $data2, $key, $children)
// {
//
// 	$value1 = $data1[$key] ?? null;
// 	$value2 = $data2[$key] ?? null;	
//     
//     if (!is_object($value1) && is_object($value2)) {
//         return ($value1);
//     }
//
// 	return null;
// }

// function genValue2($data2, $data1, $key, $children)
// {
//
// 	$value1 = $data1[$key] ?? null;
// 	$value2 = $data2[$key] ?? null;	
//
//     if (is_object($value1) && !is_object($value2)) {
//         return ($value2);
//     }
//
// 	return null;
// }


function genMeta($data1, $data2, $key, $children)
{
    $value1 = $data1[$key] ?? null;
    $value2 = $data2[$key] ?? null;
    
    // if ($key == 'setting3' || $key == 'group3' || $key == 'group2' || $key == 'group1') {
    //     print_r([$key, $value1, $value2]);
    // }
    
    //print_r([$key, $data1, $data2, $children]);
    
    // if (is_object($value1) && is_object($value2)) {
    //     return null;
    // }
    
    // if (!in_array($key, $data1) && in_array($key, $data2)) {
    //     return 'addNode';
    // }
    // if (in_array($key, $data1) && !in_array($key, $data2)) {
    //     return 'deletedNode';
    // }
    if (!array_key_exists($key, $data1) && array_key_exists($key, $data2)) {
        return 'addNode';
    }
    if (array_key_exists($key, $data1) && !array_key_exists($key, $data2)) {
        return 'deletedNode';
    }

    // if ($value1 === null) {
    //     return 'addNode';
    // }
    // if ($value2 === null) {
    //     return 'deletedNode';
    // }

    return null;
}

function genMeta2($data1, $data2, $key)
{
    $value1 = $data1[$key] ?? null;
    $value2 = $data2[$key] ?? null;
    //$result = null; 
    
    // if ($key == 'setting3' || $key == 'group3' || $key == 'group2' || $key == 'group1') {
    //     print_r([$key, $value1, $value2]);
    // }

    //print_r([$key, $value1, $value2]);
    if (!array_key_exists($key, $data1) && array_key_exists($key, $data2)) {
        return 'addNode';
    }
    if (array_key_exists($key, $data1) && !array_key_exists($key, $data2)) {
        return 'deletedNode';
    }

    return null;
    
    
    // elseif (is_object($value1)) {
    //     //$result = genTree($value1);
    //     $result = [];
    // } elseif (is_object($value2)) {
    //     //$result = genTree($value2);
    //     $result = [];
    // } else {
    //     $result = [];
    // }
    // return $result;
}

// function genTree($object)
// {
//     $data = get_object_vars($object);
//     $tree = array_reduce(array_keys($data), function ($acc, $key) use ($data) {
//         if (is_object($data[$key])) {
//             $node = [
//                 'name' => $key,
//                 'value' => null,
//                 'meta' => null,
//                 'children' => genTree($data[$key]),
//             ];
//         } else {
//             $node = [
//                 'name' => $key,
//                 'value' =>  $data[$key],
//                 'meta' => null,
//                 'children' => [],
//             ];
//         }
//         $acc[$key] = $node;
//         return $acc;
//     }, []);
//     return $tree;
// }

function getChildren($data1, $data2, $key)
{
    $value1 = $data1[$key] ?? null;
    $value2 = $data2[$key] ?? null;
    
    if (is_object($value1) && is_object($value2)) {
        $result = genDiff($value1, $value2);
    } elseif (is_object($value1)) {
        //$result = genTree($value1);
        $result = [];
    } elseif (is_object($value2)) {
        //$result = genTree($value2);
        $result = [];
    } else {
        $result = [];
    }
    return $result;
}




// function genDiff($tree1, $tree2)
// {
//     $treeMerge = array_replace_recursive($tree1, $tree2);
//     $diff = traversalMerge($treeMerge, $tree1, $tree2);
//     return $diff;
// }

// function traversalMerge($nodeMerge, $node1, $node2)
// {
//     $result = array_reduce(array_keys($nodeMerge), function ($acc, $key) use ($nodeMerge, $node1, $node2) {
//         if ($nodeMerge[$key]['children'] != []) {
//             $children = traversalMerge(
//                 $nodeMerge[$key]['children'],
//                 $node1[$key]['children'] ?? null,
//                 $node2[$key]['children'] ?? null
//             );
//         } else {
//             $children = [];
//         }
//
//         if (($node1 != null) && ($node2 != null)) {
//             $acc[$key] = getNode($nodeMerge[$key], $node1[$key] ?? null, $node2[$key] ?? null, $children);
//         } else {
//             $acc[$key] = [
//                 'value' => $nodeMerge[$key]['value'],
//                 'oldValue' => null,
//                 'meta' => null,
//                 'children' => $children
//             ];
//         }
//         return $acc;
//     }, []);
//     return $result;
// }

// function getNode($nodeMerge, $node1, $node2, $children)
// {
//     $meta = null;
//     $oldValue = null;
//     if ($node1 === null) {
//         $meta = 'add';
//     } elseif ($node2 === null) {
//         $meta = 'deleted';
//     } elseif ($node1['value'] !== $node2['value']) {
//         $meta = 'newValue';
//         $oldValue = $node1['value'];
//     }
//     return [
//         'value' => $nodeMerge['value'],
//         'oldValue' => $oldValue,
//         'meta' => $meta,
//         'children' => $children
//     ];
// }

function sortTree($node)
{
    ksort($node);
    $result = array_reduce(array_keys($node), function ($acc, $key) use ($node) {
            $acc[$key] = $node[$key];
        if ($node[$key]['children'] != []) {
            $acc[$key]['children'] = sortTree($node[$key]['children']);
        }
        return $acc;
    }, []);
    return $result;
}
