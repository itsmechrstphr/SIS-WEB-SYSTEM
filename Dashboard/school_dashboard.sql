-- school_dashboard.sql
-- Import this into phpMyAdmin to create the database and tables with some sample data.

CREATE DATABASE IF NOT EXISTS school_dashboard CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE school_dashboard;

-- Accounts (students, teachers, admins)
CREATE TABLE IF NOT EXISTS accounts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  role ENUM('student','teacher','admin') NOT NULL DEFAULT 'student',
  first_name VARCHAR(100) NOT NULL,
  last_name VARCHAR(100) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Classes
CREATE TABLE IF NOT EXISTS classes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

-- Events
CREATE TABLE IF NOT EXISTS events (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200) NOT NULL,
  date_start DATE NOT NULL,
  date_end DATE NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Schedules
CREATE TABLE IF NOT EXISTS schedules (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200) NOT NULL,
  date_start DATE NOT NULL,
  date_end DATE NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Sample data
INSERT INTO accounts (role, first_name, last_name, email, password_hash) VALUES
('student', 'Juan', 'Dela Cruz', 'juan@example.com', '$2y$10$samplehashsamplehashsamplehashsampl'),
('teacher', 'Maria', 'Santos', 'maria@example.com', '$2y$10$samplehashsamplehashsamplehashsampl'),
('teacher', 'Jose', 'Rizal', 'jose@example.com', '$2y$10$samplehashsamplehashsamplehashsampl');

INSERT INTO classes (name, is_active) VALUES
('Grade 10 - A', 1), ('Grade 10 - B', 1), ('Grade 11 - A', 0);

INSERT INTO events (title, date_start, date_end) VALUES
('Buwan ng Wika', '2025-08-01', '2025-08-31'),
('Christmas Break', '2025-12-20', '2026-01-03');

INSERT INTO schedules (title, date_start, date_end) VALUES
('First Quarter Exams', '2025-09-10', '2025-09-12'),
('Parent-Teacher Meeting', '2025-10-05', NULL);
