<?php
$pageTitle = 'Home';
require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<!-- Welcome Section -->
<div class="hero-section">
    <div class="container text-center">
        <div class="logo-container mb-4">
            <img src="/assets/images/bank-logo.png" alt="Bank Logo" style="max-width: 150px;" onerror="this.style.display='none'">
        </div>
        <h1 class="fade-in-up">Welcome to My Bank Appointment System</h1>
        <p class="lead fade-in-up">Manage your bank visits easily!</p>
        <?php if (!isLoggedIn()): ?>
            <div class="mt-4">
                <a href="/auth/register.php" class="btn btn-light btn-lg me-2">
                    <i class="fas fa-user-plus"></i> Register Now
                </a>
                <a href="/auth/login.php" class="btn btn-outline-light btn-lg">
                    <i class="fas fa-sign-in-alt"></i> Login
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="container my-5">
    <?php if (isLoggedIn() && !isAdmin()): ?>
        <!-- Quick Booking for Logged In Users (Non-Admin) -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="card fade-in-up" style="background: linear-gradient(135deg, var(--primary-green) 0%, var(--light-green) 100%); color: white;">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-calendar-plus fa-4x mb-3"></i>
                        <h3 class="mb-3">Ready to Book Your Appointment?</h3>
                        <p class="mb-4">Schedule your visit to one of our branches quickly and easily</p>
                        <a href="/pages/appointments.php" class="btn btn-light btn-lg">
                            <i class="fas fa-calendar-check"></i> Book New Appointment
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Services Cards -->
    <div class="row mb-5">
        <div class="col-12 mb-4">
            <h2 class="text-center" style="color: var(--primary-green); font-weight: 700;">Our Banking Services</h2>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card service-card fade-in-up">
                <div class="card-body">
                    <div class="icon-box">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <h4>Appointment Booking</h4>
                    <p>Book your appointment at any of our branches easily with no waiting</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card service-card fade-in-up">
                <div class="card-body">
                    <div class="icon-box">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <h4>Secure Service</h4>
                    <p>Secure and protected system to maintain the privacy of your data</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card service-card fade-in-up">
                <div class="card-body">
                    <div class="icon-box">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h4>24/7 Support</h4>
                    <p>Support team is ready to help you around the clock</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Information -->
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card fade-in-up">
                <div class="card-body">
                    <h4 style="color: var(--primary-green);"><i class="fas fa-clock"></i> Business Hours</h4>
                    <hr>
                    <p><strong>Saturday - Wednesday:</strong> 8:00 AM - 4:00 PM</p>
                    <p><strong>Thursday:</strong> 8:00 AM - 12:00 PM</p>
                    <p><strong>Friday:</strong> Closed</p>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card fade-in-up">
                <div class="card-body">
                    <h4 style="color: var(--primary-green);"><i class="fas fa-info-circle"></i> How to Book</h4>
                    <hr>
                    <ol>
                        <li>Login to your account or create a new account</li>
                        <li>Select the required service type</li>
                        <li>Choose the appropriate date and time</li>
                        <li>Receive appointment confirmation message</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <?php if (isLoggedIn() && !isAdmin()): ?>
        <!-- My Upcoming Appointments (Non-Admin Only) -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card fade-in-up">
                    <div class="card-header">
                        <h4 class="mb-0"><i class="fas fa-calendar-alt"></i> My Upcoming Appointments</h4>
                    </div>
                    <div class="card-body">
                        <?php
                        $user_id = $_SESSION['user_id'];
                        $today = date('Y-m-d');
                        $query = "SELECT * FROM appointments WHERE user_id = ? AND date >= ? AND status = 'upcoming' ORDER BY date, time LIMIT 5";
                        $stmt = $conn->prepare($query);
                        $stmt->bind_param('is', $user_id, $today);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows > 0):
                            ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                    <tr>
                                        <th>Service</th>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Status</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php while ($appointment = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><i class="fas fa-briefcase"></i> <?php echo $appointment['service']; ?></td>
                                            <td><i class="fas fa-calendar"></i> <?php echo date('Y/m/d', strtotime($appointment['date'])); ?></td>
                                            <td><i class="fas fa-clock"></i> <?php echo date('h:i A', strtotime($appointment['time'])); ?></td>
                                            <td><span class="status-upcoming">Upcoming</span></td>
                                        </tr>
                                    <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-center mt-3">
                                <a href="/pages/appointments.php" class="btn btn-outline-primary">
                                    <i class="fas fa-list"></i> View All Appointments
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No upcoming appointments</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
