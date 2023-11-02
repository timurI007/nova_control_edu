<?php

namespace App\Classes;

use DateTime;

class GlobalVariable
{
    public static $positions = [ // 'name' => id
        'teacher' => 1
    ];

    // Methods

    /**
     * Get is working time for staff
     */
    public static function getIsWorkTime($current_day, $current_time){
        if(empty($current_day)){
            return false;
        }
        foreach($current_day as $period){
            $arr = explode('-', $period);
            $time1 = DateTime::createFromFormat('H:i', $current_time);
            $time2 = DateTime::createFromFormat('H:i', $arr[0]);
            $time3 = DateTime::createFromFormat('H:i', $arr[1]);
            if ($time1 > $time2 && $time1 < $time3)
            {
               return true;
            }
        }
        return false;
    }
}