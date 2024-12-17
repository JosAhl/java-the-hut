<?php

require __DIR__ . '/../views/header.php';

/*---------- Connect to database ----------*/
$database = new PDO('sqlite:../database/yrgopelago-tatooine.db');

if (isset($_POST['transferCode'], $_POST['room'], $_POST['arrival'], $_POST['departure'])) {
    try {
        /*--- Start a transaction ---*/
        $database->beginTransaction();

        /*--- Sanitize and validate inputs ---*/
        $transferCode = trim($_POST['transferCode']);
        $room = (int)trim($_POST['room']);
        $arrival = trim($_POST['arrival']);
        $departure = trim($_POST['departure']);
        $features = $_POST['features'] ?? []; /*--- Put selected features in an array ---*/

        /*--- Insert details into bookings ---*/
        $bookingsQuery = 'INSERT INTO bookings (transferCode, room_id, arrival, departure)
                          VALUES (:transferCode, :room, :arrival, :departure)';

        $statement = $database->prepare($bookingsQuery);

        $statement->bindParam(':transferCode', $transferCode, PDO::PARAM_STR);
        $statement->bindParam(':room', $room, PDO::PARAM_INT);
        $statement->bindParam(':arrival', $arrival, PDO::PARAM_STR);
        $statement->bindParam(':departure', $departure, PDO::PARAM_STR);

        $statement->execute();

        /*--- Get the last generated booking_id ---*/
        $bookingId = $database->lastInsertId();

        /*--- Calculate number of days ---*/
        $arrivalDate = new DateTime($arrival);
        $departureDate = new DateTime($departure);
        $days = $arrivalDate->diff($departureDate)->days;

        /*--- Insert selected features ---*/
        if (!empty($features)) {
            $featureQuery = 'INSERT INTO feature_selection (booking_id, feature_id, days)
                             VALUES (:bookingId, :featureId, :days)';
            $featureStatement = $database->prepare($featureQuery);

            foreach ($features as $featureId) {
                $featureStatement->bindParam(':bookingId', $bookingId, PDO::PARAM_INT);
                $featureStatement->bindParam(':featureId', $featureId, PDO::PARAM_INT);
                $featureStatement->bindParam(':days', $days, PDO::PARAM_INT);
                $featureStatement->execute();
            }
        }

        /*--- Manually calculate total price ---*/
        $roomPriceQuery = $database->prepare('
            SELECT price_per_night * (julianday(:departure) - julianday(:arrival)) as room_price, 
                price_per_night, (julianday(:departure) - julianday(:arrival)) as nights
            FROM rooms 
            WHERE room_id = :room_id
        ');
        $roomPriceQuery->execute([
            ':departure' => $departure,
            ':arrival' => $arrival,
            ':room_id' => $room
        ]);
        $roomPriceResult = $roomPriceQuery->fetch(PDO::FETCH_ASSOC);

        /*--- Calculate feature price (default to 0 if no features) ---*/
        $featurePrice = 0;
        if (!empty($features)) {
            $featurePriceQuery = $database->prepare('
                SELECT SUM(features.price * feature_selection.days) as feature_price
                FROM feature_selection
                JOIN features ON feature_selection.feature_id = features.feature_id
                WHERE feature_selection.booking_id = :booking_id
            ');
            $featurePriceQuery->execute([':booking_id' => $bookingId]);
            $featurePriceResult = $featurePriceQuery->fetch(PDO::FETCH_ASSOC);
            $featurePrice = $featurePriceResult['feature_price'] ?? 0;
        }

        /*--- Update total price in the bookings table ---*/
        $totalPrice = ($roomPriceResult['room_price'] ?? 0) + $featurePrice;
        $manualUpdateQuery = $database->prepare('
            UPDATE bookings 
            SET total_price = :total_price 
            WHERE booking_id = :booking_id
        ');
        $manualUpdateQuery->execute([
            ':total_price' => $totalPrice,
            ':booking_id' => $bookingId
        ]);

        /*--- Commit the transaction ---*/
        $database->commit();

        /*--- Fetch and display the booking details ---*/
        $statement = $database->prepare('SELECT * FROM bookings WHERE booking_id = :bookingId');
        $statement->bindParam(':bookingId', $bookingId, PDO::PARAM_INT);
        $statement->execute();
        $guest = $statement->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        /*--- Cancel query if there is an error ---*/
        $database->rollBack();
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Missing required booking information.";
}

/*---------------------------------------- Display booking details in HTML ----------------------------------------*/

/*--- Fetch the room type ---*/
$roomTypeQuery = $database->prepare('SELECT room_type FROM rooms WHERE room_id = :room_id');
$roomTypeQuery->execute([':room_id' => $room]);
$roomTypeResult = $roomTypeQuery->fetch(PDO::FETCH_ASSOC);
$roomType = $roomTypeResult['room_type'] ?? 'Unknown Room Type';

/*--- Display booking details ---*/
?>

<h1 class="success">Booking Successful!</h1>

<p>You have booked the <strong><?php echo htmlspecialchars($roomType); ?></strong> room from
    <strong><?php echo htmlspecialchars($arrival); ?></strong> to
    <strong><?php echo htmlspecialchars($departure); ?></strong>.
</p>

<p>Selected Features:</p>

<?php if (empty($features)) { ?>
    <p>No additional features selected.</p>
<?php } else {
    /*--- Fetch selected feature names and prices ---*/
    $selectedFeaturesQuery = $database->prepare('
        SELECT features.feature_name, features.price 
        FROM feature_selection
        JOIN features ON feature_selection.feature_id = features.feature_id
        WHERE feature_selection.booking_id = :booking_id
    ');
    $selectedFeaturesQuery->execute([':booking_id' => $bookingId]);
    $selectedFeatures = $selectedFeaturesQuery->fetchAll(PDO::FETCH_ASSOC);

    echo '<ul>';
    foreach ($selectedFeatures as $feature) {
        echo '<li>' . htmlspecialchars($feature['feature_name']) . ' - $' . htmlspecialchars($feature['price']) . ' per day</li>';
    }
    echo '</ul>';
}
?>

<p><strong>Total Price:</strong> $<?php echo htmlspecialchars($totalPrice); ?></p>

<?php
require __DIR__ . '/../views/footer.php';
