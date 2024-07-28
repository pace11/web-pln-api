<?php

namespace App\Helpers;

use Carbon\Carbon;

class CustomHelpers {

    public static function isPreviousDate($date) {
        $dateToCheck = Carbon::parse($date);
        $now = Carbon::now();
        $previousMonth = $now->copy()->subMonth();
        $nextMonth = $now->copy()->addMonth();
        $status = 'prev';

        if ($dateToCheck->year > $now->year) $status = "next";

        if ($dateToCheck->year < $now->year) $status = "prev";

        if ($dateToCheck->year == $now->year) {
            if ($dateToCheck->month < $now->month) $status = 'prev';
            if ($dateToCheck->month > $now->month) $status = 'next';
            if ($dateToCheck->month == $now->month) $status = 'current';
        }
        
        return $status;
    }

}