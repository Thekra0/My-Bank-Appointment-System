<?php
$pageTitle = 'Login';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

// If user is already logged in, redirect to homepage
if (isLoggedIn()) {
    header('Location: /index.php');
    exit();
}

// Process login form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = cleanInput($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        setMessage('Please enter email and password', 'error');
    } else {
        $query = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['role'] = $user['role'];

                setMessage('Welcome ' . $user['name'], 'success');
                header('Location: /index.php');
                exit();
            } else {
                setMessage('Incorrect password', 'error');
            }
        } else {
            setMessage('Email not found', 'error');
        }
    }
    header('Location: login.php');
    exit();
}
?>

<div class="auth-container">
    <div class="auth-card fade-in-up">
        <div class="logo-container">
            <img src="/assets/images/bank-logo.png" alt="Bank Logo" onerror="this.style.display='none'">
        </div>
        <h2><i class="fas fa-sign-in-alt"></i> Login</h2>

        <form method="POST" action="">
            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>

            <button type="submit" class="btn btn-primary w-100 mb-3">
                <i class="fas fa-sign-in-alt"></i> Sign In
            </button>

            <div class="text-center">
                <p>Don't have an account? <a href="register.php">Register Now</a></p>
            </div>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
