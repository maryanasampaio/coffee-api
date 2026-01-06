CREATE DATABASE IF NOT EXISTS coffee_api;
USE coffee_api;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    drink_counter INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE drink_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    date DATE NOT NULL,
    quantity INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

SELECT u.name, SUM(dl.quantity) AS quantity
FROM drink_logs dl
JOIN users u ON u.id = dl.user_id
WHERE dl.date >= CURDATE() - INTERVAL :days DAY
GROUP BY u.id, u.name
ORDER BY quantity DESC;

SELECT u.name, SUM(dl.quantity) AS quantity
FROM drink_logs dl
JOIN users u ON u.id = dl.user_id
WHERE dl.date = :date
GROUP BY u.id, u.name
ORDER BY quantity DESC;
