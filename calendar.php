<?php

declare(strict_types=1);

require 'vendor/autoload.php';

use benhall14\phpCalendar\Calendar as Calendar;

$calendar = new Calendar;

$calendar->useMondayStartingDate();

$calendar->addTableClasses('class-1 class-2 class-3');
