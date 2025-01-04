<?php
require __DIR__ . '/views/header.php';
require __DIR__ . '/processing/calendar.php';
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

    <form action="/processing/form.php" method="post">
        <section class="form-guest">
            <label for="name" class="text-input">Name</label>
            <input type="text" name="name" id="name" class="form-input">
            <label for="transferCode" class="text-input">Transfercode</label>
            <input type="text" name="transferCode" id="transferCode" class="form-input">
        </section>

        <section class="form-room">
            <label for="room" class="select-input">Room</label>
            <select name="room" id="room" class="form-input">
                <option value="1">Economy</option>
                <option value="2">Standard</option>
                <option value="3">Luxury</option>
            </select>
        </section>

        <section class="dates">
            <label for="arrival">Arrival:</label>
            <input type="date" name="arrival" id="arrival" min="2025-01-01" max="2025-01-31" required>
            <label for="departure">Departure:</label>
            <input type="date" name="departure" id="departure" min="2025-01-01" max="2025-01-31" required>
        </section>

        <div class="box">
            <input type="checkbox" name="features[]" class="form-input" value="1">
            Sauna
        </div>
        <div class="box">
            <input type="checkbox" name="features[]" class="form-input" value="2">
            Bicycle
        </div>
        <div class="box">
            <input type="checkbox" name="features[]" class="form-input" value="3">
            Java bar
        </div>
        <div class="box">
            <input type="checkbox" name="features[]" class="form-input" value="4">
            TV
        </div>
        <div class="box">
            <input type="checkbox" name="features[]" class="form-input" value="5">
            Lightsaber
        </div>
        <div class="box">
            <input type="checkbox" name="features[]" class="form-input" value="6">
            Car
        </div>

        <input type="hidden" name="response_format" value="json">
        <button name="booking" type="submit" class="book-button">Book</button>
    </form>

</section>

<section class="room-info">

    <h3>Economy</h3>
    <section class="room">
        <img src="https://2book.se/wp-content/uploads/Star-wars.jpg" alt="Budget room" class="room">
        <p>
            Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
            Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
            Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.
            Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
        </p>
        <?php echo $calendarHTML1; ?>
    </section>

    <h3>Standard</h3>
    <section class="room">
        <img src="https://2book.se/wp-content/uploads/Star-wars.jpg" alt="Standard room" class="room">
        <p>
            Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
            Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
            Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.
            Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
        </p>
        <?php echo $calendarHTML2; ?>
    </section>

    <h3>Luxury</h3>
    <section class="room">
        <img src="https://2book.se/wp-content/uploads/Star-wars.jpg" alt="Luxury room" class="room">
        <p>
            Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
            Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
            Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.
            Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
        </p>
        <?php echo $calendarHTML3; ?>
    </section>
</section>

<?php
require __DIR__ . '/views/footer.php';
