# Java the Hut

This website is made for a school project that involves building a small website or web page for 'Yrgopelago'. I made it with the intent of following specific guidelines and potential extra points as 1-5 stars. During the presentation, we will also have the opportunity to gain points by making reservations at our classmates' hotels.

## Usage 

The project is meant to be presented in group, as me and my classmates have been given API-keys allowing us to have a bank account, get a transfercode and pay for hotel rooms and features on other classmates sites. 
When visiting this site and having access to the API you may use a transfer code to select dates, a room, and features, and pay accordingly. This will connect to the API, transfering the "money" to my account while adding the details to a local database.

## Installation

While building this small website I used

- $ composer require benhall14/php-calendar
- $ composer require guzzlehttp/guzzle:^7.0

to display the available dates for booking and to fetch API endpoints.

## Database setup

CREATE TABLE IF NOT EXISTS rooms (
room_id INTEGER PRIMARY KEY autoincrement, 
room_type VARCHAR(50), 
price_per_day DECIMAL(10, 2)
);

INSERT INTO rooms (room_type, price_per_night)
VALUES
("economy", 1),
("standard", 2),
("luxury", 4);

CREATE TABLE IF NOT EXISTS features (
feature_id INTEGER PRIMARY KEY autoincrement, 
feature_name VARCHAR(50), 
price DECIMAL(10, 2)
);

INSERT INTO features (feature_name, price)
VALUES
("Bathtub", 1),
("Pool", 2),
("Bicycle", 2),
("Superior bar", 4),
("TV", 4),
("Lightsaber", 5),
("Car", 5),
("Rubiks cube", 5);

CREATE TABLE IF NOT EXISTS bookings (
booking_id INTEGER PRIMARY KEY autoincrement, 
guest VARCHAR(50),
transferCode VARCHAR(50), 
room_id INT,
arrival DATE NOT NULL,
departure DATE NOT NULL,
total_price REAL DEFAULT 0,
FOREIGN KEY (room_id) REFERENCES rooms(room_id),
CHECK (departure > arrival)
);

CREATE TABLE IF NOT EXISTS feature_selection (
selection_id INTEGER PRIMARY KEY autoincrement,
booking_id INT,
feature_id INT,
FOREIGN KEY (booking_id) REFERENCES bookings(booking_id),
FOREIGN KEY (feature_id) REFERENCES features(feature_id));

# This connects to a database designed to store information

- Keep information about the guest and their transfercode
- Keep information about which room the guest booked
- Keep information about which features the guest booked
- Keep information about the dates the guest booked
- Keep information about the total price of what the guest booked