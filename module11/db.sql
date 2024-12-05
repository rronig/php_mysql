CREATE DATABASE user_dashboard;
CREATE TABLE users(
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL
);
INSERT INTO users (username, email)
VALUES
('user1', 'user1@gmail.com'),
('user2', 'user2@gmail.com'),
('user3', 'user3@gmail.com'),
('user4', 'user4@gmail.com'),
('user5', 'user5@gmail.com');