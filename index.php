<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
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

            <div class="box">
                <input type="checkbox" name="features" class="form-input">
                Sauna
            </div>
            <div class="box">
                <input type="checkbox" name="features" class="form-input">
                Bicycle
            </div>
            <div class="box">
                <input type="checkbox" name="features" class="form-input">
                Java bar
            </div>
            <div class="box">
                <input type="checkbox" name="features" class="form-input">
                TV
            </div>
            <div class="box">
                <input type="checkbox" name="features" class="form-input">
                Lightsaber
            </div>
            <div class="box">
                <input type="checkbox" name="features" class="form-input">
                Car
            </div>

            <button name="booking" type="submit" class="book-button">Book</button>
        </form>

    </section>


</body>

</html>