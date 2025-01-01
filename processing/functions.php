<?php

declare(strict_types=1);

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

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

/*---------- Function to validate transfer code with API ----------*/

function validateTransferCode(string $transferCode, float $totalCost): bool
{
    $client = new Client([
        'base_uri' => 'https://www.yrgopelago.se/centralbank/',
        'timeout'  => 5.0,
    ]);

    try {
        $response = $client->post('transferCode', [
            'json' => [
                'transferCode' => $transferCode,
                'totalcost' => $totalCost
            ],
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);

        $body = (string) $response->getBody();
        error_log("API Response: " . $body);

        $result = json_decode($body, true);

        if (isset($result['valid']) && $result['valid'] === true) {
            return true;
        } else {
            error_log("Transfer code validation failed: " . json_encode($result));
            return false;
        }
    } catch (GuzzleException $e) {
        error_log("Guzzle error: " . $e->getMessage());
        return false;
    }
}

function processPayment(string $transferCode, string $username): bool
{
    $client = new Client([
        'base_uri' => 'https://www.yrgopelago.se/centralbank/',
        'timeout'  => 5.0,
    ]);

    try {
        /*--- JSON format ---*/
        $response = $client->post('deposit', [
            'json' => [
                'user' => $username,
                'transferCode' => $transferCode
            ],
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);

        $body = (string) $response->getBody();
        $result = json_decode($body, true);

        return isset($result['message']) && strpos(strtolower($result['message']), 'success') !== false;
    } catch (GuzzleException $e) {
        error_log("Payment processing error: " . $e->getMessage());
        return false;
    }
}
