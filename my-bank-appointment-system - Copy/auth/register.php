<?php
$pageTitle = 'Register New Account';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

// If user is already logged in, redirect to homepage
if (isLoggedIn()) {
    header('Location: /index.php');
    exit();
}

// Process registration form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = cleanInput($_POST['name']);
    $email = cleanInput($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate data
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        setMessage('Please fill in all fields', 'error');
    } elseif ($password !== $confirm_password) {
        setMessage('Passwords do not match', 'error');
    } elseif (strlen($password) < 6) {
        setMessage('Password must be at least 6 characters', 'error');
    } else {
        // Check if email already exists
        $check_query = "SELECT id FROM users WHERE email = ?";
        $stmt = $conn->prepare($check_query);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            setMessage('Email already registered', 'error');
        } else {
            // Create account
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $insert_query = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'user')";
            $stmt = $conn->prepare($insert_query);
            $stmt->bind_param('sss', $name, $email, $hashed_password);

            if ($stmt->execute()) {
                setMessage('Account created successfully! You can now log in', 'success');
                header('Location: login.php');
                exit();
            } else {
                setMessage('An error occurred while creating the account', 'error');
            }
        }
    }
    header('Location: register.php');
    exit();
}
?>

<div class="auth-container">
    <div class="auth-card fade-in-up">
        <div class="logo-container">
            <img src="/assets/images/bank-logo.png" alt="Bank Logo" onerror="this.style.display='none'">
        </div>
        <h2><i class="fas fa-user-plus"></i> Register New Account</h2>

        <form method="POST" action="">
            <div class="mb-3">
                <label for="name" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required minlength="6">
                <small class="text-muted">Must be at least 6 characters</small>
            </div>

            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required minlength="6">
            </div>

            <button type="submit" class="btn btn-primary w-100 mb-3">
                <i class="fas fa-user-plus"></i> Create Account
            </button>

            <div class="text-center">
                <p>Already have an account? <a href="login.php">Login</a></p>
            </div>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
