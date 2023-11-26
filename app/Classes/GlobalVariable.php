<?php

namespace App\Classes;

use DateTime;

class GlobalVariable
{
    // Database
    public static $positions = [ // 'name' => id
        'teacher' => 1
    ];

    // Changeable
    public static $groups_status = [
        0, // recruitment
        1, // studying
        2, // suspended
        3, // finished
    ];
    public static $groups_labels = [
        'Recruitment',
        'Studying',
        'Suspended',
        'Finished',
    ];
    public static $groups_styles = [
        'info', // recruitment
        'success', // studying
        'warning', // suspended
        'info', // finished
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

    /**
     * Gets group statuses optional
     */
    public static function get_group_status_optional(){
        $res = array();
        foreach(self::$groups_status as $i){
            $res[$i] = self::$groups_labels[$i];
        }
        return $res;
    }

    /**
     * Gets group statuses STYLES optional
     */
    public static function get_group_styles_optional(){
        $res = array();
        foreach(self::$groups_status as $i){
            $res[$i] = self::$groups_styles[$i];
        }
        return $res;
    }
}