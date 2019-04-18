<?php
// 排序数
$sort_array = [1, 6, 8, 4, 5, 2, 7, 3, 9,7];

/**
 * 冒泡排序
 * @param $sort_array
 */
function bubble(&$sort_array)
{
    // 取出数组长度
    $count = count($sort_array);
    for ($i = 0; $i < $count; $i++) {
        // 进行冒泡排序，进行两两比较，交换
        for ($j = 0; $j < $count - $i; $j++) {
            if ($sort_array[$j] > $sort_array[$j + 1]) {
                $temp = $sort_array[$j];
                $sort_array[$j] = $sort_array[$j + 1];
                $sort_array[$j + 1] = $temp;
            }
        }
    }
}
//echo '冒泡排序'.PHP_EOL;
//bubble($sort_array);
//var_dump($sort_array);

function quickSort($sort_array_input){
    if(!isset($sort_array_input[1])){
        return $sort_array_input;
    }
    $mid = $sort_array_input[0]; // 用于分割的关键字，一般是首个元素
    $left_array = array();
    $right_array = array();
    for ($i = 1; $i < count($sort_array_input); $i++) {
        if ($sort_array_input[$i] < $mid) {
            $left_array[] = $sort_array_input[$i];
        } else {
            $right_array[] = $sort_array_input[$i];
        }
    }
    $left_array = quickSort($left_array); // 把比较小的数组再一次分割
    $left_array[] = $mid; // 把分割的元素加到小的数组后面
    $right_array = quickSort($right_array); // 把比较大的数组再一次分割
    return array_merge($left_array,$right_array);
}
echo '快速排序'.PHP_EOL;
$temp = quickSort($sort_array);
var_dump($temp);exit;