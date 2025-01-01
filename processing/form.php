<?php

require __DIR__ . '/../views/header.php';
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/functions.php';

/*---------- Connect to database ----------*/
$database = new PDO('sqlite:../database/yrgopelago-tatooine.db');

if (isset($_POST['name'], $_POST['transferCode'], $_POST['room'], $_POST['arrival'], $_POST['departure'])) {
    try {
        /*--- Start a transaction ---*/
        $database->beginTransaction();

        /*--- Sanitize and validate inputs ---*/
        $guest = trim($_POST['name']);
        $transferCode = trim($_POST['transferCode']);
        $room = (int)trim($_POST['room']);
        $arrival = trim($_POST['arrival']);
        $departure = trim($_POST['departure']);
        $features = $_POST['features'] ?? [];

        /*--- Check if dates are available for chosen room ---*/
        if (!isRoomAvailable($database, $room, $arrival, $departure)) { ?>
            <h1 class="error">Booking Failed</h1>
            <p class="error-message">Selected dates are not available for this room. Please choose different dates.</p>
<?php
            exit;
        }

        /*--- Calculate total price first (needed for transfer code validation) ---*/
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

        /*--- Calculate number of days ---*/
        $arrivalDate = new DateTime($arrival);
        $departureDate = new DateTime($departure);
        $days = $arrivalDate->diff($departureDate)->days;

        /*--- Calculate feature prices ---*/
        $featurePrice = 0;
        if (!empty($features)) {
            $featurePriceQuery = $database->prepare('
                SELECT SUM(price) * :days as total_feature_price
                FROM features
                WHERE feature_id IN (' . str_repeat('?,', count($features) - 1) . '?)
            ');
            $featurePriceQuery->execute(array_merge([$days], $features));
            $featurePriceResult = $featurePriceQuery->fetch(PDO::FETCH_ASSOC);
            $featurePrice = $featurePriceResult['total_feature_price'] ?? 0;
        }

        $totalPrice = ($roomPriceResult['room_price'] ?? 0) + $featurePrice;

        /*--- Validate transfer code with centralbank using Guzzle ---*/
        if (!isValidUuid($transferCode)) {
            echo json_encode(['error' => 'Invalid transfer code format.']);
            exit;
        }

        /*--- Format the total price ---*/
        $totalPrice = (float)number_format((float)$totalPrice, 2, '.', '');
        error_log("Attempting validation with transferCode: {$transferCode} and totalcost: {$totalPrice}");

        try {
            if (!validateTransferCode($transferCode, $totalPrice)) {
                echo json_encode(['error' => 'Invalid or already used transfer code. Please check your transfer code and try again.']);
                exit;
            }

            if (!processPayment($transferCode, "Josefine")) {
                echo json_encode(['error' => 'Payment processing failed. Please try again or contact support.']);
                exit;
            }

            /*--- If valid details, proceed with booking ---*/
            $bookingsQuery = 'INSERT INTO bookings (guest, transferCode, room_id, arrival, departure, total_price)
                              VALUES (:guest, :transferCode, :room, :arrival, :departure, :total_price)';
            $statement = $database->prepare($bookingsQuery);
            $statement->execute([
                ':guest' => $guest,
                ':transferCode' => $transferCode,
                ':room' => $room,
                ':arrival' => $arrival,
                ':departure' => $departure,
                ':total_price' => $totalPrice
            ]);

            $bookingId = $database->lastInsertId();

            /*--- Insert selected features ---*/
            if (!empty($features)) {
                $featureQuery = 'INSERT INTO feature_selection (booking_id, feature_id, days)
                                 VALUES (:bookingId, :featureId, :days)';
                $featureStatement = $database->prepare($featureQuery);

                foreach ($features as $featureId) {
                    $featureStatement->execute([
                        ':bookingId' => $bookingId,
                        ':featureId' => $featureId,
                        ':days' => $days
                    ]);
                }
            }

            /*--- Commit the transaction ---*/
            $database->commit();

            /*--- Return success response as JSON ---*/
            $response = [
                'island' => 'Tatooine',
                'hotel' => 'Java the Hut',
                'arrival_date' => $arrivalDate->format('Y-m-d'),
                'departure_date' => $departureDate->format('Y-m-d'),
                'total_cost' => $totalPrice,
                'stars' => '4',
                'features' => $selectedFeatures,
                'additional_info' => [
                    'greeting' => 'Thank you for choosing Java the Hut',
                    'imageUrl' => 'https://c.tenor.com/Ijo0JkXZnKMAAAAC/tenor.gif'
                ]
            ];

            echo json_encode($response);
        } catch (Exception $e) {
            /*--- Rollback if any error occurs ---*/
            $database->rollBack();
            echo json_encode(['error' => 'Booking failed: ' . $e->getMessage()]);
        }
    } catch (Exception $e) {
        /*--- Rollback if any error occurs in the beginning ---*/
        $database->rollBack();
        echo json_encode(['error' => 'Booking failed: ' . $e->getMessage()]);
    }
} else {
    echo "Missing required booking information.";
}

require __DIR__ . '/../views/footer.php';
