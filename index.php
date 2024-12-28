<?php
require __DIR__ . '/views/header.php';
require __DIR__ . '/calendar.php';
?>

<section aria-label="header">
    <header>
        <h1>Java the Hut</h1>
        <section class="star-rating">
            <img src="/assets/pngkey.com-deathstar-png-1240061.png" class="stars">
            <img src="/assets/pngkey.com-deathstar-png-1240061.png" class="stars">
            <img src="/assets/pngkey.com-deathstar-png-1240061.png" class="stars">
            <img src="/assets/pngkey.com-deathstar-png-1240061.png" class="stars">
            <img src="/assets/pngkey.com-deathstar-png-1240061.png" class="stars">
        </section>
        <nav>
            <a href="#">Hotel</a>
            <a href="#">Reservation</a>
            <a href="#">About</a>
        </nav>
    </header>
</section>

<section class="features">
    <h2>Welcome to Java the Hut</h2>
    <p>
        Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
        Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
        Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.
        Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
    </p>
    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/6c/Star_Wars_Logo.svg/2560px-Star_Wars_Logo.svg.png">

    <h2>Prepare for your stay</h2>

    <section class="feature-info">

        <h3>Sauna</h3>
        <img src="https://lumiere-a.akamaihd.net/v1/images/yodas-hut_a3d1133d.jpeg?region=0%2C117%2C1560%2C783" alt="Sauna">
        <p>
            Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
            Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
            Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.
            Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
        </p>

    </section>

    <section class="feature-info">
        <h3>Bicycle</h3>
        <p>
            Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
            Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
            Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.
            Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
        </p>
        <img src="https://www.blacksbricks.de/images/product_images/original_images/mandobikeis9.jpg" alt="Bicycle">
    </section>

    <section class="feature-info">
        <h3>Java bar</h3>
        <img src="https://images.fineartamerica.com/images/artworkimages/medium/2/jabba-the-hutt-jeremy-guerin.jpg" alt="Java bar">
        <p>
            Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
            Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
            Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.
            Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
        </p>
    </section>

    <section class="feature-info">
        <h3>TV</h3>
        <p>
            Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
            Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
            Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.
            Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
        </p>
        <img src="https://lumiere-a.akamaihd.net/v1/images/image_77ccbb93.jpeg?region=156%2C0%2C967%2C544" alt="TV hologram">
    </section>

    <section class="feature-info">
        <h3>Lightsaber</h3>
        <img src="https://assets-prd.ignimgs.com/2021/11/12/untitled-4-1636733283239.jpg" alt="Lightsaber">
        <p>
            Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
            Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
            Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.
            Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
        </p>
    </section>

    <section class="feature-info">
        <h3>Car</h3>
        <p>
            Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
            Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
            Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.
            Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
        </p>
        <img src="https://lumiere-a.akamaihd.net/v1/images/databank_podracer_01_169_89a8621d.jpeg?region=0%2C0%2C1560%2C780" alt="Car">
    </section>
</section>

<section class="booking-form">

    <form action="/processing/booking.php" method="post">
        <label for="name" class="text-input">Name</label>
        <input type="text" name="name" class="form-input">
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
            <input type="date" name="arrival" min="2025-01-01" max="2025-01-31" required>
            <label for="departure">Departure:</label>
            <input type="date" name="departure" min="2025-01-01" max="2025-01-31" required>
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
