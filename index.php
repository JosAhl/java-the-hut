<?php
require __DIR__ . '/views/header.php';
require __DIR__ . '/processing/calendar.php';
require __DIR__ . '/processing/functions.php';
$rooms = getRooms();
$features = getFeatures();
?>

<section class="welcome">

    <h2>Welcome to Java the Hut</h2>
    <p>
        Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
        Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
        Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.
        Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
    </p>

</section>

<section class="booking-form">
    <h1 class="form-title">Book</h1>

    <form action="./processing/form.php" method="post">
        <section class="form-guest">
            <label for="name" class="text-input">Name</label>
            <input type="text" name="name" id="name" class="form-input">
            <label for="transferCode" class="text-input">Transfercode</label>
            <input type="text" name="transferCode" id="transferCode" class="form-input">
        </section>


        <section class="dates">
            <label for="arrival">Arrival:</label>
            <input type="date" name="arrival" id="arrival" min="2025-01-01" max="2025-01-31" required>
            <label for="departure">Departure:</label>
            <input type="date" name="departure" id="departure" min="2025-01-01" max="2025-01-31" required>
        </section>

        <section class="form-room">
            <label for="room" class="select-input">Room</label>
            <select name="room" id="room" class="form-input">
                <?php foreach ($rooms as $room): ?>
                    <option value="<?php echo htmlspecialchars($room['room_id']); ?>">
                        <?php echo htmlspecialchars($room['room_type']); ?> | <?php echo htmlspecialchars($room['price_per_night']); ?>/night
                    </option>
                <?php endforeach; ?>
            </select>
        </section>

        <section class="feature-box">
            <?php foreach ($features as $feature): ?>
                <div class="box">
                    <input type="checkbox" name="features[]" class="form-input" value="<?php echo htmlspecialchars($feature['feature_id']); ?>">
                    <?php echo htmlspecialchars($feature['price']); ?>
                    <img src="./assets/coin.png" alt="Coin" class="cost-img">
                    <?php echo htmlspecialchars($feature['feature_name']); ?>
                </div>
            <?php endforeach; ?>
        </section>

        <input type="hidden" name="response_format" value="json">
        <section class="book-button">
            <button name="booking" type="submit" class="book-button">Book</button>
        </section>
    </form>

</section>

<section class="room-info">

    <h3>Economy</h3>
    <section class="room">
        <img src="./assets/economy.png" alt="Budget room" class="room">
        <p>
            This cozy mud hut offers rustic charm with its earthy clay walls, warm ambient lighting, and essential furnishings.
            Including a view of moisture farms and sandstorms at no extra charge this hut is perfect for travelers seeking
            simplicity and an authentic experience.
        </p>
        <?php echo $calendarHTML1; ?>
    </section>

    <h3>Standard</h3>
    <section class="room">
        <img src="./assets/standard.png" alt="Standard room" class="room">
        <p>
            Enjoy a modest yet comfortable stay in our advanced mud hut. Featuring polished clay walls, soft linens, and
            glowing sconces, this room blends rustic charm with subtle modern conveniences. Ideal for guests who appreciate
            a balance of tradition and comfort.
        </p>
        <?php echo $calendarHTML2; ?>
    </section>

    <h3>Luxury</h3>
    <section class="room">
        <img src="./assets/luxury.png" alt="Luxury room" class="room">
        <p>
            Who says luxury can't be found on Tatooine? Our luxury space tower suite sits atop a well-known sand dune with the most
            exclusive view of the twin suns setting. Perfect for stargazing and maybe a little quiet contemplation high above the
            frequent sandstorms.
        </p>
        <?php echo $calendarHTML3; ?>
    </section>
</section>

<?php
require __DIR__ . '/views/footer.php';
