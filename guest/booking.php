<?php

require __DIR__ . '/../views/header.php';

/*---------- Connect to database ----------*/
$database = new PDO('sqlite:../database/yrgopelago-tatooine.db');

if (isset($_POST['transferCode'], $_POST['room'], $_POST['arrival'], $_POST['departure'], $_POST['features'])) {
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
        $featureQuery = 'INSERT INTO feature_selection (booking_id, feature_id, days)
                         VALUES (:bookingId, :featureId, :days)';
        $featureStatement = $database->prepare($featureQuery);

        foreach ($features as $featureId) {
            $featureStatement->bindParam(':bookingId', $bookingId, PDO::PARAM_INT);
            $featureStatement->bindParam(':featureId', $featureId, PDO::PARAM_INT);
            $featureStatement->bindParam(':days', $days, PDO::PARAM_INT);
            $featureStatement->execute();
        }

        /*--- Commit the transaction ---*/
        $database->commit();

        /*--- Fetch and display the booking details ---*/
        $statement = $database->prepare('SELECT * FROM bookings WHERE booking_id = :bookingId');
        $statement->bindParam(':bookingId', $bookingId, PDO::PARAM_INT);
        $statement->execute();
        $guest = $statement->fetch(PDO::FETCH_ASSOC);

        var_dump($guest);
    } catch (PDOException $e) {
        /*--- Cancel query if there is an error ---*/
        $database->rollBack();
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Missing required booking information.";
}

require __DIR__ . '/../views/footer.php';
