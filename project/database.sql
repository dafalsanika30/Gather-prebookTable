CREATE TABLE restaurants (
    id SERIAL PRIMARY KEY,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    restaurant_name VARCHAR(255) NOT NULL,
    address TEXT NOT NULL,
    mobile_number VARCHAR(10) NOT NULL,
    cuisine_type VARCHAR(100),
    features TEXT,
    menu_image_path TEXT,          
    restaurant_image_path TEXT ,    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    seating_capacity INT,  
    availability_status VARCHAR(20),  
    business_hours VARCHAR(255),
    description VARCHAR(500) NOT NULL
);

DELETE restaurants

CREATE TABLE customers (
    id SERIAL PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password TEXT NOT NULL,
    first_name VARCHAR(255) NOT NULL,
    last_name VARCHAR(255) NOT NULL,
    mobile_number VARCHAR(15) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE items (
    item_id SERIAL PRIMARY KEY,
    item_name VARCHAR(100) NOT NULL,
    price INT NOT NULL,
    description VARCHAR(300),
    specialty VARCHAR(3) CHECK (specialty IN ('yes', 'no')),
    restaurant_id INT,
    FOREIGN KEY (restaurant_id) REFERENCES restaurants (id) ON DELETE CASCADE
);

CREATE TABLE bookings (
    booking_id SERIAL PRIMARY KEY,
    cid INT REFERENCES customers(id) ON DELETE CASCADE,
    rid INT REFERENCES restaurants(id) ON DELETE CASCADE,
    booking_date DATE NOT NULL,
    booking_time TIME NOT NULL,
    num_guests INT NOT NULL,
    special_requests TEXT,
    status VARCHAR(50) DEFAULT 'Pending',  -- e.g., Pending, Confirmed, Canceled
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE tables (
    table_id SERIAL PRIMARY KEY,
    id INT REFERENCES restaurants(id),
    table_number INT,
    seats INT,
    available BOOLEAN DEFAULT TRUE
);

CREATE TABLE booking_details (
    booking_detail_id SERIAL PRIMARY KEY,
    booking_id INT REFERENCES bookings(booking_id) ON DELETE CASCADE,
    item_id INT REFERENCES items(item_id) ON DELETE CASCADE,
    quantity INT,
    price INT
);
CREATE TABLE feedback (
    feedback_id SERIAL PRIMARY KEY,
    restaurant_id INT REFERENCES restaurants(id) ON DELETE CASCADE,
    customer_id INT REFERENCES customers(id) ON DELETE CASCADE,
    feedback_text TEXT NOT NULL,
    rating INT CHECK (rating >= 1 AND rating <= 5), -- Ratings between 1 and 5
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

SELECT distinct bookings.booking_date, bookings.booking_time, restaurants.restaurant_name, bookings.status,items.item_name FROM bookings,restaurants,items,booking_details where bookings.rid = restaurants.id and items.item_id=booking_details.item_id and bookings.cid=14;

SELECT 
    feedback.feedback_id, 
    customers.first_name || ' ' || customers.last_name AS customer_name, 
    restaurants.restaurant_name, 
    feedback.feedback_text, 
    feedback.created_at 
FROM 
    feedback
INNER JOIN 
    customers ON feedback.customer_id = customers.id
INNER JOIN 
    restaurants ON feedback.restaurant_id = restaurants.id
WHERE
    feedback.restaurant_id= 1
ORDER BY 
    feedback.created_at DESC;

ALTER TABLE bookings ADD COLUMN customer_started_order BOOLEAN DEFAULT FALSE;


SELECT 
    bookings.booking_id,
    bookings.booking_date,
    bookings.booking_time,
    customers.first_name,
    customers.last_name,
    bookings.status,
    bookings.num_guests,
    booking_details.quantity,
    STRING_AGG(items.item_name, ', ') AS item_names
FROM 
    bookings
JOIN 
    restaurants ON bookings.rid = restaurants.id
JOIN 
    booking_details ON bookings.booking_id = booking_details.booking_id
JOIN 
    items ON booking_details.item_id = items.item_id
JOIN
    customers ON bookings.cid=customers.id
WHERE 
    bookings.rid=1
GROUP BY 
    bookings.booking_id, 
    bookings.booking_date, 
    bookings.booking_time, 
    restaurants.restaurant_name, 
    bookings.status,
    bookings.num_guests,
    booking_details.quantity,
    customers.first_name,
    customers.last_name;

SELECT 
    bookings.booking_id,
    bookings.booking_date,
    bookings.booking_time,
    customers.first_name,
    customers.last_name,
    bookings.status,
    bookings.num_guests,
    STRING_AGG(item_details.item_info, ', ') AS item_names_with_quantities
FROM 
    bookings
JOIN 
    restaurants ON bookings.rid = restaurants.id
JOIN 
    customers ON bookings.cid = customers.id
-- Subquery to calculate total quantity per item
JOIN (
    SELECT
        booking_details.booking_id,
        items.item_name,
        SUM(booking_details.quantity) AS total_quantity,
        items.item_name || ' (' || SUM(booking_details.quantity) || ')' AS item_info
    FROM
        booking_details
    JOIN 
        items ON booking_details.item_id = items.item_id
    GROUP BY
        booking_details.booking_id,
        items.item_name
) AS item_details ON bookings.booking_id = item_details.booking_id
WHERE 
    bookings.rid = 1
GROUP BY 
    bookings.booking_id, 
    bookings.booking_date, 
    bookings.booking_time, 
    customers.first_name, 
    customers.last_name, 
    bookings.status, 
    bookings.num_guests;
