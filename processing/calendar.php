<?php

declare(strict_types=1);

require __DIR__ . ' /../vendor/autoload.php';

use benhall14\phpCalendar\Calendar as Calendar;

// Database connection
$database = new PDO('sqlite:' . __DIR__ . '/../database/yrgopelago-tatooine.db'); // Adjust path as needed

function getBookedDates(PDO $database, int $roomId): array
{
    $query = $database->prepare('
        SELECT arrival, departure 
        FROM bookings 
        WHERE room_id = :room_id
    ');

    $query->execute([':room_id' => $roomId]);
    $bookings = $query->fetchAll(PDO::FETCH_ASSOC);

    $bookedDates = [];

    foreach ($bookings as $booking) {
        $start = new DateTime($booking['arrival']);
        $end = new DateTime($booking['departure']);
        $end->modify('+1 day');
        $interval = new DateInterval('P1D');
        $dateRange = new DatePeriod($start, $interval, $end);

        foreach ($dateRange as $date) {
            $bookedDates[] = $date->format('Y-m-d');
        }
    }

    return $bookedDates;
}

function generateCalendar(int $year, int $month, int $roomId, PDO $database)
{
    $calendar = new Calendar;
    $calendar->useMondayStartingDate();
    $calendar->addTableClasses('class-1 class-2 class-3');

    // Get booked dates for this room
    $bookedDates = getBookedDates($database, $roomId);
    foreach ($bookedDates as $date) {
        // Pass the date twice (start and end date) and 'Booked' as the event text
        $calendar->addEvent($date, $date, 'b');
    }

    $date = new DateTime("$year-$month-01");
    return $calendar->draw($date);
    /*$html = $calendar->draw($date);*/

    return str_replace(
        '<div class="cal-event-box"></div>',
        '<div class="cal-event-box b"></div>',
        $html
    );
    /*
    return str_replace(
        '<div class="cal-event-box"></div>',
        '<div class="cal-event-box b"></div>',
        $html
    );
    */
}

// Function to get booked dates as JSON for JavaScript
function getBookedDatesJson(PDO $database, int $roomId): string
{
    return json_encode(getBookedDates($database, $roomId));
}

// Generate calendars for each room
$calendarHTML1 = generateCalendar(2025, 1, 1, $database); // Economy
$calendarHTML2 = generateCalendar(2025, 1, 2, $database); // Standard
$calendarHTML3 = generateCalendar(2025, 1, 3, $database); // Luxury