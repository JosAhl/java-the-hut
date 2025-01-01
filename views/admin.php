<?php
/*--- Session to stay logged in while updating database ---*/
session_start();

define("API_KEY", "04b59177-e288-498d-b0c5-5e4042dae806");

$message = '';

$access_granted = isset($_SESSION['api_key']) && $_SESSION['api_key'] === API_KEY;

/*--- API key login ---*/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['api_key']) && $_POST['api_key'] === API_KEY) {
        $_SESSION['api_key'] = API_KEY;  /* Store API key to stay in session */
        $access_granted = true;
    } elseif (isset($_POST['api_key']) && $_POST['api_key'] !== API_KEY) {
        $message = "Access Denied: Invalid API Key.";
        $access_granted = false;
    }

    /*--- Handle room and feature price updates ---*/
    if ($access_granted && isset($_POST['update_room']) || isset($_POST['update_feature'])) {
        if (isset($_POST['update_room'])) {
            try {
                $db = new PDO('sqlite:../database/yrgopelago-tatooine.db');
                $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $stmt = $db->prepare("UPDATE rooms SET price_per_night = :price WHERE room_id = :id");
                $stmt->execute([
                    ':price' => $_POST['price'],
                    ':id' => $_POST['room_id']
                ]);
                $message = "Room price updated!";
            } catch (PDOException $e) {
                $message = "Database error: " . $e->getMessage();
            }
        }

        if (isset($_POST['update_feature'])) {
            try {
                $db = new PDO('sqlite:../database/yrgopelago-tatooine.db');
                $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $stmt = $db->prepare("UPDATE features SET price = :price WHERE feature_id = :id");
                $stmt->execute([
                    ':price' => $_POST['price'],
                    ':id' => $_POST['feature_id']
                ]);
                $message = "Feature price updated!";
            } catch (PDOException $e) {
                $message = "Database error: " . $e->getMessage();
            }
        }
    }
}

/*--- Get current data when logged in ---*/
if ($access_granted) {
    try {
        $db = new PDO('sqlite:../database/yrgopelago-tatooine.db');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $rooms = $db->query("SELECT * FROM rooms")->fetchAll(PDO::FETCH_ASSOC);
        $features = $db->query("SELECT * FROM features")->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link rel="stylesheet" type="text/css" href="/css/admin.css">
</head>

<body>
    <div class="container">
        <h1>Admin</h1>

        <?php if (!$access_granted): ?>
            <!--- Display form to enter API key --->
            <div class="login-form">
                <h2>Login with API Key</h2>
                <form method="POST">
                    <input type="text" name="api_key" placeholder="Enter API Key" required>
                    <button type="submit">Login</button>
                </form>
                <?php if ($message): ?>
                    <div class="error"><?php echo htmlspecialchars($message); ?></div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <!-- Display admin panel when logged in -->
            <?php if (isset($message)): ?>
                <div class="message"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <div class="section">
                <h2>Room Prices</h2>
                <table>
                    <tr>
                        <th>Room Type</th>
                        <th>Current Price</th>
                        <th>Action</th>
                    </tr>
                    <?php foreach ($rooms as $room): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($room['room_type']); ?></td>
                            <td>$<?php echo htmlspecialchars($room['price_per_night']); ?></td>
                            <td>
                                <form method="POST">
                                    <input type="hidden" name="room_id" value="<?php echo $room['room_id']; ?>">
                                    <input type="number" name="price" step="1" value="<?php echo $room['price_per_night']; ?>" required>
                                    <button type="submit" name="update_room">Update</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>

            <div class="section">
                <h2>Feature Prices</h2>
                <table>
                    <tr>
                        <th>Feature</th>
                        <th>Current Price</th>
                        <th>Action</th>
                    </tr>
                    <?php foreach ($features as $feature): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($feature['feature_name']); ?></td>
                            <td>$<?php echo htmlspecialchars($feature['price']); ?></td>
                            <td>
                                <form method="POST">
                                    <input type="hidden" name="feature_id" value="<?php echo $feature['feature_id']; ?>">
                                    <input type="number" name="price" step="1" value="<?php echo $feature['price']; ?>" required>
                                    <button type="submit" name="update_feature">Update</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>