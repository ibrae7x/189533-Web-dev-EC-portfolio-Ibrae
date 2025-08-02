-- Complete Ibrae Portfolio Database Setup
-- This file contains everything needed for the portfolio

-- Drop database if exists and create fresh
DROP DATABASE IF EXISTS ibrae_portfolio;
CREATE DATABASE ibrae_portfolio CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE ibrae_portfolio;

-- Users table for authentication (sign-up/sign-in)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    is_active BOOLEAN DEFAULT TRUE,
    INDEX idx_email (email),
    INDEX idx_active (is_active)
);

-- Contacts table for contact form submissions
CREATE TABLE contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    subject VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    newsletter BOOLEAN DEFAULT FALSE,
    ip_address VARCHAR(45),
    status ENUM('new', 'read', 'replied') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_status (status),
    INDEX idx_created (created_at)
);

-- Insert sample users with properly hashed passwords
-- Default password for all test users is "password123"
INSERT INTO users (first_name, last_name, email, password) VALUES
('Ibrae', 'Mamo', 'ibrae@strathmore.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('John', 'Doe', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Jane', 'Smith', 'jane@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Test', 'User', 'test@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Insert sample contact messages
INSERT INTO contacts (first_name, last_name, email, phone, subject, message, newsletter) VALUES
('Sarah', 'Wilson', 'sarah@example.com', '+254700123456', 'collaboration', 'Hi Ibrae! I would love to collaborate on a project together. Please get back to me when you have time.', FALSE),
('Mike', 'Johnson', 'mike@company.com', '+254700987654', 'internship', 'We have an exciting internship opportunity at our tech startup. Would you be interested in learning more?', TRUE),
('Emma', 'Davis', 'emma@university.edu', NULL, 'project', 'I saw your portfolio and I am impressed! Would you like to work on a university project with our team?', FALSE),
('Alex', 'Brown', 'alex@techcorp.com', '+254700555777', 'general', 'Your work looks amazing! I would like to discuss some potential opportunities with you.', TRUE);

-- Create indexes for better performance
CREATE INDEX idx_users_email_active ON users(email, is_active);
CREATE INDEX idx_contacts_created_status ON contacts(created_at, status);

-- Show table structures and data
SELECT 'USERS TABLE STRUCTURE:' as info;
DESCRIBE users;

SELECT 'CONTACTS TABLE STRUCTURE:' as info;
DESCRIBE contacts;

SELECT 'SAMPLE USERS:' as info;
SELECT id, first_name, last_name, email, created_at, is_active FROM users;

SELECT 'SAMPLE CONTACTS:' as info;
SELECT id, first_name, last_name, email, subject, status, created_at FROM contacts;

SELECT 'DATABASE SETUP COMPLETE!' as status;
