<?php
require __DIR__ . '/views/header.php';
require __DIR__ . '/calendar.php';
?>


<section class="booking-form">

    <form action="/guest/booking.php" method="post">
        <label for="transferCode" class="text-input">Transfercode</label>
        <input type="text" name="transferCode" class="form-input">

        <br>
        <label for="room" class="select-input">Room</label>
        <select name="room" class="form-input">
            <option value="1">Economy</option>
            <option value="2">Standard</option>
            <option value="3">Luxury</option>
        </select>

        <section class="dates">
            <label for="arrival">Arrival:</label>
            <input type="date" name="arrival" required>
            <label for="departure">Departure:</label>
            <input type="date" name="departure" required>
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

        <button name="booking" type="submit" class="book-button">Book</button>
    </form>

</section>


<?php
require __DIR__ . '/views/footer.php';
