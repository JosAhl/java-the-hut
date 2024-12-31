<?php

// Include header and autoload
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

        /*--- If payment successful, proceed with booking ---*/
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

        /*--- Display success message ---*/
        $roomTypeQuery = $database->prepare('SELECT room_type FROM rooms WHERE room_id = :room_id');
        $roomTypeQuery->execute([':room_id' => $room]);
        $roomTypeResult = $roomTypeQuery->fetch(PDO::FETCH_ASSOC);
        $roomType = $roomTypeResult['room_type'] ?? 'Unknown Room Type';

        /*--- Display booking confirmation ---*/
        ?>
        <h1 class="success">Booking Successful!</h1>

        <p>You have booked the <strong><?= htmlspecialchars($roomType) ?></strong> room from
            <strong><?= htmlspecialchars($arrival) ?></strong> to
            <strong><?= htmlspecialchars($departure) ?></strong>.
        </p>

        <p>Selected Features:</p>

        <?php if (empty($features)) : ?>
            <p>No additional features selected.</p>
        <?php else :
            $selectedFeaturesQuery = $database->prepare('
                SELECT features.feature_name, features.price 
                FROM feature_selection
                JOIN features ON feature_selection.feature_id = features.feature_id
                WHERE feature_selection.booking_id = :booking_id
            ');
            $selectedFeaturesQuery->execute([':booking_id' => $bookingId]);
            $selectedFeatures = $selectedFeaturesQuery->fetchAll(PDO::FETCH_ASSOC);
        ?>
            <ul>
                <?php foreach ($selectedFeatures as $feature) : ?>
                    <li><?= htmlspecialchars($feature['feature_name']) ?> - $<?= htmlspecialchars($feature['price']) ?> per day</li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <p><strong>Total Price:</strong> $<?= htmlspecialchars($totalPrice) ?></p>

    <?php
    } catch (Exception $e) {
        /*--- Cancel query if there is an error ---*/
        $database->rollBack();
    ?>
        <h1 class="error">Booking Failed</h1>
        <p class="error-message"><?= htmlspecialchars($e->getMessage()) ?></p>
<?php
    }
} else {
    echo "Missing required booking information.";
}

require __DIR__ . '/../views/footer.php';
