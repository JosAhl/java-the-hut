<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Exception\ClientException;

/*-------------------- Function to check valid transfercode format --------------------*/

function isValidUuid($uuid)
{
    return preg_match('/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$/', $uuid);
}

/*-------------------- Function to check valid available dates --------------------*/
function isRoomAvailable($database, $room, $arrival, $departure)
{
    $query = $database->prepare('
        SELECT COUNT(*) AS count
        FROM bookings
        WHERE room_id = :room_id
          AND (
              (arrival <= :arrival AND departure > :arrival)
              OR (arrival < :departure AND departure >= :departure)
              OR (arrival >= :arrival AND departure <= :departure)
          )
    ');
    $query->execute([
        ':room_id' => $room,
        ':arrival' => $arrival,
        ':departure' => $departure,
    ]);

    $result = $query->fetch(PDO::FETCH_ASSOC);
    return $result['count'] == 0; /* Room is available if count is 0 */
}

/*-------------------- Function to check total cost --------------------*/
function calculateTotalPrice($database, $room, $arrival, $departure, $features)
{

    /*--- Calculate room price ---*/
    $roomPriceQuery = $database->prepare('
        SELECT price_per_night * (julianday(:departure) - julianday(:arrival)) as room_price
        FROM rooms 
        WHERE room_id = :room_id
    ');
    $roomPriceQuery->execute([
        ':departure' => $departure,
        ':arrival' => $arrival,
        ':room_id' => $room
    ]);
    $roomPriceResult = $roomPriceQuery->fetch(PDO::FETCH_ASSOC);

    /*--- Calculate feature prices ---*/
    $featurePrice = 0;
    if (!empty($features)) {
        $featurePriceQuery = $database->prepare('
            SELECT SUM(price) as total_feature_price
            FROM features
            WHERE feature_id IN (' . str_repeat('?,', count($features) - 1) . '?)
        ');
        $featurePriceQuery->execute(array_merge($features));
        $featurePriceResult = $featurePriceQuery->fetch(PDO::FETCH_ASSOC);
        $featurePrice = $featurePriceResult['total_feature_price'] ?? 0;
    }

    return ($roomPriceResult['room_price'] ?? 0) + $featurePrice;
}

/*-------------------- Function to fetch features to display on index --------------------*/
function getFeatures()
{
    try {
        $db = new PDO('sqlite:' . __DIR__ . '/../database/yrgopelago-tatooine.db');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $features = $db->query("SELECT * FROM features ORDER BY feature_id")->fetchAll(PDO::FETCH_ASSOC);
        return $features;
    } catch (PDOException $e) {
        /* Return empty array if database connection fails */
        return [];
    }
}

/*-------------------- Function to fetch rooms to display on index --------------------*/
function getRooms()
{
    try {
        $db = new PDO('sqlite:' . __DIR__ . '/../database/yrgopelago-tatooine.db');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $rooms = $db->query("SELECT * FROM rooms ORDER BY room_id")->fetchAll(PDO::FETCH_ASSOC);
        return $rooms;
    } catch (PDOException $e) {
        /* Return empty array if database connection fails */
        return [];
    }
}

/*-------------------- Function to fetch transfercode endpoint --------------------*/
function checkTransferCode($bookingData)
{
    $transferCode = $bookingData['transfercode'];
    $totalCost = $bookingData['totalcost'];

    try {
        $client = new GuzzleHttp\Client(['verify' => false]); /* Disable SSL verification */
        $res = $client->request('POST', 'https://yrgopelago.se/centralbank/transferCode', [
            'form_params' => [
                'transferCode' => $transferCode,
                'totalcost' => $totalCost
            ]
        ]);
        $body = (string) $res->getBody();
        return json_decode($body, true);
    } catch (ClientException $e) {
        $response = $e->getResponse();
        $errorContent = $response->getBody()->getContents();
        return json_decode($errorContent, true);
    }
}

/*-------------------- Function to fetch deposit endpoint --------------------*/
function processPayment($transferCode, $username)
{
    try {
        $client = new GuzzleHttp\Client(['verify' => false]); /* Disable SSL verification */
        $res = $client->request('POST', 'https://yrgopelago.se/centralbank/deposit', [
            'form_params' => [
                'user' => $username,
                'transferCode' => $transferCode,
                'numberOfDays' => 3
            ]
        ]);
        $body = (string) $res->getBody();
        $responseBody = json_decode($body, true);
        return isset($responseBody['status']) && $responseBody['status'] === 'success';
    } catch (ClientException $e) {
        error_log('Error processing payment: ' . $e->getMessage());
        return false;
    }
}
