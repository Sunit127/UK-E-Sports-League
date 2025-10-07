-- Create database
-- CREATE DATABASE IF NOT EXISTS esports;
-- USE esports;

-- Create user table for admin login
CREATE TABLE IF NOT EXISTS user (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert admin user (password: password123)
INSERT INTO user (username, password) VALUES 
('admin', 'password123');

-- Create team table
CREATE TABLE IF NOT EXISTS team (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    city VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample teams
INSERT INTO team (name, city) VALUES 
('Team Alpha', 'London'),
('Team Beta', 'Manchester'),
('Team Gamma', 'Birmingham'),
('Team Delta', 'Liverpool'),
('Team Epsilon', 'Leeds');

-- Create participant table
CREATE TABLE IF NOT EXISTS participant (
    id INT PRIMARY KEY AUTO_INCREMENT,
    gamertag VARCHAR(50) NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    team_id INT,
    kills INT DEFAULT 0,
    deaths INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (team_id) REFERENCES team(id) ON DELETE SET NULL
);

-- Insert sample participants
INSERT INTO participant (gamertag, name, email, team_id, kills, deaths) VALUES 
('NinjaKing', 'John Smith', 'john@example.com', 1, 45, 12),
('ShadowHunter', 'Emma Wilson', 'emma@example.com', 1, 38, 15),
('DragonSlayer', 'Mike Johnson', 'mike@example.com', 2, 52, 8),
('PhoenixRise', 'Sarah Davis', 'sarah@example.com', 2, 41, 18),
('CyberWarrior', 'Alex Chen', 'alex@example.com', 3, 33, 22),
('StormBreaker', 'Lisa Brown', 'lisa@example.com', 3, 47, 11),
('ThunderBolt', 'James Taylor', 'james@example.com', 4, 29, 25),
('IceQueen', 'Maria Garcia', 'maria@example.com', 4, 44, 13),
('FireStorm', 'David Lee', 'david@example.com', 5, 36, 19),
('NightFury', 'Anna White', 'anna@example.com', 5, 50, 10);

-- Create merchandise table for registration
CREATE TABLE IF NOT EXISTS merchandise (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(15) NOT NULL,
    address TEXT NOT NULL,
    tshirt_size VARCHAR(10) NOT NULL,
    registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);