-- Create the database
CREATE DATABASE IF NOT EXISTS attendance_db;
USE attendance_db;

-- Admin table
CREATE TABLE IF NOT EXISTS admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- Students table
CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    roll_number VARCHAR(50) NOT NULL UNIQUE,
    class VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Attendance table
CREATE TABLE IF NOT EXISTS attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    date DATE NOT NULL,
    status ENUM('Present', 'Absent') NOT NULL,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    UNIQUE KEY unique_attendance (student_id, date)
);

-- Insert default admin (password: admin123)
INSERT INTO admin (username, password) VALUES (
    'admin', 
    '$2y$10$uXRYW.hPXk.3KZnD2lnTL.uHfjy8L/Tq9CGvMRPe8neQr79zXxK8C'
);

-- Sample student data (optional)
INSERT INTO students (name, roll_number, class) VALUES
('John Doe', 'S001', 'Class 10'),
('Jane Smith', 'S002', 'Class 10'),
('Michael Johnson', 'S003', 'Class 11');

-- Sample attendance data (optional)
INSERT INTO attendance (student_id, date, status) VALUES
(1, CURDATE(), 'Present'),
(2, CURDATE(), 'Present'),
(3, CURDATE(), 'Absent');