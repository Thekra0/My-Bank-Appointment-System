<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'bank_appointment_system');

// Create database connection
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Check connection
    if ($conn->connect_error) {
        die("Database connection failed: " . $conn->connect_error);
    }

    // Set UTF-8 encoding
    $conn->set_charset("utf8mb4");

} catch (Exception $e) {
    die("Connection error: " . $e->getMessage());
}

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Check if user is admin
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Protect pages - require login
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /auth/login.php');
        exit();
    }
}

// Clean input data
function cleanInput($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $conn->real_escape_string($data);
}

// Set notification message
function setMessage($message, $type = 'success') {
    $_SESSION['message'] = $message;
    $_SESSION['message_type'] = $type;
}

// Get and clear notification message
function getMessage() {
    if (isset($_SESSION['message'])) {
        $message = $_SESSION['message'];
        $type = $_SESSION['message_type'] ?? 'success';
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
        return ['message' => $message, 'type' => $type];
    }
    return null;
}

// Automatically mark past appointments as completed
function autoCompletePastAppointments() {
    global $conn;
    
    // Get current date and time
    $current_datetime = date('Y-m-d H:i:s');
    
    // Update appointments where date and time have passed and status is still 'upcoming'
    $query = "UPDATE appointments 
              SET status = 'completed' 
              WHERE status = 'upcoming' 
              AND (date < CURDATE() OR (date = CURDATE() AND time < CURTIME()))";
    
    $conn->query($query);
}
?>
