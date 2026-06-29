
-- Bank Appointment System Database
CREATE DATABASE IF NOT EXISTS bank_appointment_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE bank_appointment_system;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Branches Table
CREATE TABLE IF NOT EXISTS branches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    address TEXT NOT NULL,
    map_embed TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Appointments Table
CREATE TABLE IF NOT EXISTS appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    branch_id INT NOT NULL,
    service VARCHAR(100) NOT NULL,
    date DATE NOT NULL,
    time TIME NOT NULL,
    status ENUM('upcoming', 'completed', 'cancelled') DEFAULT 'upcoming',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Ratings Table
CREATE TABLE IF NOT EXISTS ratings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    appointment_id INT NOT NULL,
    rating INT CHECK (rating BETWEEN 1 AND 5),
    feedback TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample Data

-- Create admin account (admin@bank.com / password)
INSERT INTO users (name, email, password, role) VALUES
('System Administrator', 'admin@bank.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('John Smith', 'john@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user'),
('Sarah Johnson', 'sarah@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user');

-- Sample branches (Saudi Arabia) - moved before appointments
INSERT INTO branches (name, address, map_embed) VALUES
('Main Branch', 'King Fahd Road, Riyadh, Saudi Arabia', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3624.2882389641307!2d46.67529631500215!3d24.71355198413092!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3e2f034cbcbda8a7%3A0x3b2e01a7e3a3e7f9!2sKing%20Fahd%20Rd%2C%20Riyadh%2C%20Saudi%20Arabia!5e0!3m2!1sar!2ssa!4v1728456789012!5m2!1sar!2ssa'),
('Jeddah Branch', 'Al Tahlia Street, Al Rawdah District, Jeddah, Saudi Arabia', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3621.647146291094!2d39.15220991500481!3d21.543333885710247!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x15c3d099c9c84c4b%3A0x15b7ff9f7e19f1f7!2sAl%20Rawdah%2C%20Jeddah%20Saudi%20Arabia!5e0!3m2!1sar!2ssa!4v1728456789013!5m2!1sar!2ssa'),
('Dammam Branch', 'King Abdulaziz Road, Dammam, Saudi Arabia', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3478.952590174139!2d50.09709331511999!3d26.42068148333638!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3e3624f66b1a63e5%3A0x85871f2a0bdb1b5!2sDammam%20Saudi%20Arabia!5e0!3m2!1sar!2ssa!4v1728456789014!5m2!1sar!2ssa');

-- Sample appointments
INSERT INTO appointments (user_id, branch_id, service, date, time, status) VALUES
(2, 1, 'Open New Account', '2025-10-15', '10:00:00', 'upcoming'),
(2, 2, 'Loan Inquiry', '2025-10-12', '11:30:00', 'upcoming'),
(3, 1, 'Update Account Information', '2025-10-10', '09:00:00', 'completed'),
(3, 3, 'Credit Card Application', '2025-10-20', '14:00:00', 'upcoming');

-- Sample ratings
INSERT INTO ratings (user_id, appointment_id, rating, feedback) VALUES
(3, 3, 5, 'Excellent and fast service, thank you very much!'),
(2, 1, 4, 'Very good but there was a slight waiting time');

-- Note: Default password for all users is: password
