<?php

declare(strict_types=1);

/*---------- Function to check if transfercode is in correct format ----------*/

function isValidUuid(string $uuid): bool
{

    if (!is_string($uuid) || (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $uuid) !== 1)) {
        return false;
    }

    return true;
}

/*---------- Function to check if dates are available ----------*/

function isRoomAvailable(PDO $database, int $roomId, string $arrival, string $departure): bool
{
    $query = $database->prepare('
        SELECT COUNT(*) as booking_count
        FROM bookings
        WHERE room_id = :room_id
        AND (
            (:arrival BETWEEN arrival AND DATE(departure, "-1 day"))
            OR
            (:departure BETWEEN DATE(arrival, "+1 day") AND departure)
            OR
            (arrival BETWEEN :arrival AND :departure)
        )
    ');

    $query->execute([
        ':room_id' => $roomId,
        ':arrival' => $arrival,
        ':departure' => $departure
    ]);

    $result = $query->fetch(PDO::FETCH_ASSOC);
    return $result['booking_count'] === 0;
}
