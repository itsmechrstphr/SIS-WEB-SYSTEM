-- Student Management System Database Schema
CREATE DATABASE IF NOT EXISTS student_management_system;
USE student_management_system;

-- Users table for authentication
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'faculty', 'student') NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Events table for school events
CREATE TABLE IF NOT EXISTS events (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    event_date DATE NOT NULL,
    event_time TIME,
    location VARCHAR(255),
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Class schedules table
CREATE TABLE IF NOT EXISTS schedules (
    id INT PRIMARY KEY AUTO_INCREMENT,
    class_name VARCHAR(100) NOT NULL,
    subject VARCHAR(100) NOT NULL,
    faculty_id INT,
    day_of_week ENUM('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'),
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    room VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (faculty_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Grades table
CREATE TABLE IF NOT EXISTS grades (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT,
    subject VARCHAR(100) NOT NULL,
    grade DECIMAL(4,2) NOT NULL,
    semester VARCHAR(20),
    academic_year VARCHAR(20),
    faculty_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (faculty_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Attendance table
CREATE TABLE IF NOT EXISTS attendance (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT,
    subject VARCHAR(100) NOT NULL,
    attendance_date DATE NOT NULL,
    status ENUM('present', 'absent', 'late') NOT NULL,
    faculty_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (faculty_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Notifications table
CREATE TABLE IF NOT EXISTS notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    sender_id INT,
    receiver_id INT,
    notification_type ENUM('event', 'grade', 'attendance', 'general') NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert default admin user
INSERT INTO users (username, email, password, role, first_name, last_name) VALUES
('admin', 'admin@school.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'System', 'Administrator');

-- Insert sample faculty
INSERT INTO users (username, email, password, role, first_name, last_name) VALUES
('prof.smith', 'smith@school.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'faculty', 'John', 'Smith'),
('dr.jones', 'jones@school.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'faculty', 'Sarah', 'Jones');

-- Insert sample students
INSERT INTO users (username, email, password, role, first_name, last_name) VALUES
('student1', 'student1@school.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 'Alice', 'Johnson'),
('student2', 'student2@school.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 'Bob', 'Williams'),
('student3', 'student3@school.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 'Charlie', 'Brown');
