<?php

declare(strict_types=1);

require 'vendor/autoload.php';

use benhall14\phpCalendar\Calendar as Calendar;

function generateCalendar(int $year, int $month)
{
    $calendar = new Calendar;

    /*--- Start calendar on Monday ---*/
    $calendar->useMondayStartingDate();

    /*--- Add custom CSS classes to the calendar table (you can customize this per calendar) ---*/
    $calendar->addTableClasses('class-1 class-2 class-3');

    $date = new DateTime("$year-$month-01");
    /*--- Render calender for January 2025 ---*/
    return $calendar->draw($date);
}

$calendarHTML1 = generateCalendar(2025, 1);
$calendarHTML2 = generateCalendar(2025, 1);
$calendarHTML3 = generateCalendar(2025, 1);
