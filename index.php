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


        <section class="dates">
            <label for="arrival">Arrival:</label>
            <input type="date" name="arrival" id="arrival" min="2025-01-01" max="2025-01-31" required>
            <label for="departure">Departure:</label>
            <input type="date" name="departure" id="departure" min="2025-01-01" max="2025-01-31" required>
        </section>

        <section class="form-room">
            <label for="room" class="select-input">Room</label>
            <select name="room" id="room" class="form-input">
                <option value="1">Economy | 1/night</option>
                <option value="2">Standard | 2/night</option>
                <option value="3">Luxury | 4/night</option>
            </select>
        </section>

        <section class="feature-box">
            <div class="box">
                <input type="checkbox" name="features[]" class="form-input" value="1">
                Bathtub <img src="/assets/coin.png" alt="Coin" class="cost-img">1/night
            </div>
            <div class="box">
                <input type="checkbox" name="features[]" class="form-input" value="2">
                Pool <img src="/assets/coin.png" alt="Coin" class="cost-img">2/night
            </div>
            <div class="box">
                <input type="checkbox" name="features[]" class="form-input" value="3">
                Bicycle <img src="/assets/coin.png" alt="Coin" class="cost-img">2/night
            </div>
            <div class="box">
                <input type="checkbox" name="features[]" class="form-input" value="4">
                Superior bar <img src="/assets/coin.png" alt="Coin" class="cost-img">4/night
            </div>
            <div class="box">
                <input type="checkbox" name="features[]" class="form-input" value="5">
                TV <img src="/assets/coin.png" alt="Coin" class="cost-img">4/night
            </div>
            <div class="box">
                <input type="checkbox" name="features[]" class="form-input" value="6">
                Lightsaber <img src="/assets/coin.png" alt="Coin" class="cost-img">5/night
            </div>
            <div class="box">
                <input type="checkbox" name="features[]" class="form-input" value="7">
                Car <img src="/assets/coin.png" alt="Coin" class="cost-img">5/night
            </div>
            <div class="box">
                <input type="checkbox" name="features[]" class="form-input" value="8">
                Rubiks Cube <img src="/assets/coin.png" alt="Coin" class="cost-img">5/night
            </div>
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
        <img src="/assets/economy.png" alt="Budget room" class="room">
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
        <img src="/assets/standard.png" alt="Standard room" class="room">
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
        <img src="/assets/luxury.png" alt="Luxury room" class="room">
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
