<?php
/**
 * Created by PhpStorm.
 * User: DIE
 * Date: 16/09/2021
 * Time: 09:48
 */

function DateFormat($originalDate)
{
//    $timestamp = strtotime($originalDate);
//    $newDate = date("m-d-Y", $timestamp);
    $date = date_create($originalDate);
    $newDate =  date_format($date,"Y-m-d");
    return $newDate;
}
?>
