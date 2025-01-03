<?php

session_start();
require_once 'functions.php';
require __DIR__ . '/../vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new PDO('sqlite:' . __DIR__ . '/../database/yrgopelago-tatooine.db');

    $guest = $_POST['name'];
    $room = $_POST['room'];
    $arrival = $_POST['arrival'];
    $departure = $_POST['departure'];
    $transferCode = $_POST['transferCode'];
    $features = isset($_POST['features']) ? $_POST['features'] : [];
    $responseFormat = isset($_POST['response_format']) && $_POST['response_format'] === 'json';

    /* --- Calculate total price --- */
    $totalPrice = calculateTotalPrice($database, $room, $arrival, $departure, $features);

    $arrivalDate = new DateTime($arrival);
    $departureDate = new DateTime($departure);
    $days = $arrivalDate->diff($departureDate)->days;

    $bookingData = [
        'guest' => $guest,
        'room' => $room,
        'arrival' => $arrival,
        'departure' => $departure,
        'transfercode' => $transferCode,
        'totalcost' => $totalPrice,
        'days' => $days
    ];

    /* --- Check room availability --- */
    if (!isRoomAvailable($database, $bookingData['room'], $bookingData['arrival'], $bookingData['departure'])) {
        $_SESSION['errors'][] = 'The room is not available for the selected dates.';
        if ($responseFormat) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'The room is not available for the selected dates.']);
        } else {
            header('Location: /');
        }
        exit;
    }

    /* --- Validate transfer code --- */
    if (!isValidUuid($bookingData['transfercode'])) {
        $_SESSION['errors'][] = 'Your transfer code is not valid.';
        if ($responseFormat) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Your transfer code is not valid.']);
        } else {
            header('Location: /');
        }
        exit;
    }

    $transferCodeResult = checkTransferCode($bookingData);

    if (isset($transferCodeResult['status']) && $transferCodeResult['status'] === 'success') {
        $paymentResult = processPayment($bookingData['transfercode'], 'Josefine');
        if ($paymentResult === true) {
            try {
                $database->beginTransaction();
                /*--- If valid details, proceed with booking ---*/
                $bookingsQuery = 'INSERT INTO bookings (guest, transferCode, room_id, arrival, departure, total_price)
                                VALUES (:guest, :transferCode, :room, :arrival, :departure, :total_price)';
                $statement = $database->prepare($bookingsQuery);
                $statement->execute([
                    ':guest' => $bookingData['guest'],
                    ':transferCode' => $bookingData['transfercode'],
                    ':room' => $bookingData['room'],
                    ':arrival' => $bookingData['arrival'],
                    ':departure' => $bookingData['departure'],
                    ':total_price' => $bookingData['totalcost']
                ]);

                $bookingId = $database->lastInsertId();

                /*--- Insert selected features ---*/
                if (!empty($bookingData['features'])) {
                    $featureQuery = 'INSERT INTO feature_selection (booking_id, feature_id, days)
                                 VALUES (:bookingId, :featureId, :days)';
                    $featureStatement = $database->prepare($featureQuery);

                    foreach ($bookingData['features'] as $featureId) {
                        $featureStatement->execute([
                            ':bookingId' => $bookingId,
                            ':featureId' => $featureId,
                            ':days' => $bookingData['days']
                        ]);
                    }
                }

                $database->commit();

                /*--- Return successful booking as JSON ---*/
                $_SESSION['messages'][] = 'Booking successfully completed!';
                if ($responseFormat) {
                    header('Content-Type: application/json');
                    echo json_encode(
                        [
                            'status' => 'success',
                            'message' => 'Booking successfully completed!',
                            'island' => 'Tatooine',
                            'hotel' => 'Java the Hut',
                            'arrival_date' => $arrival,
                            'departure_date' => $departure,
                            'total_cost' => $totalPrice,
                            'stars' => '4',
                            'features' => $features,
                            'additional_info' => [
                                'greeting' => 'Thank you for choosing Java the Hut',
                                'imageUrl' => 'https://c.tenor.com/Ijo0JkXZnKMAAAAC/tenor.gif'
                            ]
                        ]
                    );
                } else {
                    header('Location: /');
                }
            } catch (PDOException $e) {
                $database->rollBack();
                $_SESSION['errors'][] = 'Database error occurred. Please try again.';
                if ($responseFormat) {
                    header('Content-Type: application/json');
                    echo json_encode(['status' => 'error', 'message' => 'Database error occurred. Please try again.']);
                } else {
                    header('Location: /');
                }
            }
        } else {
            /*--- Return payment error as JSON ---*/
            $_SESSION['errors'][] = 'Payment failed: Insufficient funds for this booking.';
            if ($responseFormat) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'Payment failed: Insufficient funds for this booking.']);
            } else {
                header('Location: /');
            }
        }
    } else {
        /*--- Return tranfercode error as JSON ---*/
        $_SESSION['errors'][] = 'Invalid transfer code.';
        if ($responseFormat) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Invalid transfer code.']);
        } else {
            header('Location: /');
        }
    }
}
