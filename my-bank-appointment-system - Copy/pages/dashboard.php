<?php
$pageTitle = 'Dashboard';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

// Protect page - admins only
requireLogin();
if (!isAdmin()) {
    header('Location: /index.php');
    exit();
}

// Cancel appointment (admin action)
if (isset($_GET['cancel_appointment']) && is_numeric($_GET['cancel_appointment'])) {
    $appointment_id = (int)$_GET['cancel_appointment'];

    $query = "UPDATE appointments SET status = 'cancelled' WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $appointment_id);

    if ($stmt->execute()) {
        setMessage('Appointment cancelled successfully!', 'success');
    } else {
        setMessage('An error occurred while cancelling the appointment', 'error');
    }
    header('Location: dashboard.php');
    exit();
}

// Mark appointment as completed (admin action)
if (isset($_GET['complete_appointment']) && is_numeric($_GET['complete_appointment'])) {
    $appointment_id = (int)$_GET['complete_appointment'];

    $query = "UPDATE appointments SET status = 'completed' WHERE id = ? AND status = 'upcoming'";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $appointment_id);

    if ($stmt->execute()) {
        setMessage('Appointment marked as completed successfully!', 'success');
    } else {
        setMessage('An error occurred while marking the appointment as completed', 'error');
    }
    header('Location: dashboard.php');
    exit();
}

// Appointment statistics
$stats = [];

// Upcoming appointments count
$query = "SELECT COUNT(*) as count FROM appointments WHERE status = 'upcoming'";
$result = $conn->query($query);
$stats['upcoming'] = $result->fetch_assoc()['count'];

// Completed appointments count
$query = "SELECT COUNT(*) as count FROM appointments WHERE status = 'completed'";
$result = $conn->query($query);
$stats['completed'] = $result->fetch_assoc()['count'];

// Cancelled appointments count
$query = "SELECT COUNT(*) as count FROM appointments WHERE status = 'cancelled'";
$result = $conn->query($query);
$stats['cancelled'] = $result->fetch_assoc()['count'];

// Total users
$query = "SELECT COUNT(*) as count FROM users WHERE role = 'user'";
$result = $conn->query($query);
$stats['users'] = $result->fetch_assoc()['count'];

// Total appointments
$query = "SELECT COUNT(*) as count FROM appointments";
$result = $conn->query($query);
$stats['total_appointments'] = $result->fetch_assoc()['count'];

// Average rating
$query = "SELECT AVG(rating) as avg_rating, COUNT(*) as total_ratings FROM ratings";
$result = $conn->query($query);
$rating_data = $result->fetch_assoc();
$stats['avg_rating'] = round($rating_data['avg_rating'], 1);
$stats['total_ratings'] = $rating_data['total_ratings'];

// Recent appointments
$query = "SELECT a.*, u.name as user_name, b.name as branch_name FROM appointments a
          JOIN users u ON a.user_id = u.id
          LEFT JOIN branches b ON a.branch_id = b.id
          ORDER BY a.created_at DESC LIMIT 10";
$recent_appointments = $conn->query($query);

// Most requested services statistics
$query = "SELECT service, COUNT(*) as count FROM appointments GROUP BY service ORDER BY count DESC LIMIT 5";
$popular_services = $conn->query($query);
?>

<div class="container my-5">
    <div class="row mb-4">
        <div class="col-12">
            <h2 style="color: var(--primary-green);"><i class="fas fa-chart-bar"></i> Admin Dashboard</h2>
            <p class="text-muted">System statistics overview</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-5">
        <div class="col-md-3 mb-3">
            <div class="stat-card fade-in-up">
                <i class="fas fa-calendar-check"></i>
                <h3><?php echo $stats['upcoming']; ?></h3>
                <p>Upcoming Appointments</p>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card fade-in-up" style="background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);">
                <i class="fas fa-check-circle"></i>
                <h3><?php echo $stats['completed']; ?></h3>
                <p>Completed Appointments</p>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card fade-in-up" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);">
                <i class="fas fa-ban"></i>
                <h3><?php echo $stats['cancelled']; ?></h3>
                <p>Cancelled Appointments</p>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card fade-in-up" style="background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);">
                <i class="fas fa-users"></i>
                <h3><?php echo $stats['users']; ?></h3>
                <p>Total Users</p>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row mb-5">
        <div class="col-lg-6 mb-4">
            <div class="card fade-in-up h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-pie"></i> Appointment Status Distribution</h5>
                </div>
                <div class="card-body">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="card fade-in-up h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Most Requested Services</h5>
                </div>
                <div class="card-body">
                    <canvas id="servicesChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Information -->
    <div class="row mb-5">
        <div class="col-lg-4 mb-4">
            <div class="card fade-in-up h-100">
                <div class="card-body text-center">
                    <i class="fas fa-calendar-alt fa-3x mb-3" style="color: var(--primary-green);"></i>
                    <h3 style="color: var(--primary-green);"><?php echo $stats['total_appointments']; ?></h3>
                    <p class="mb-0">Total Booked Appointments</p>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-4">
            <div class="card fade-in-up h-100">
                <div class="card-body text-center">
                    <i class="fas fa-star fa-3x mb-3" style="color: #ffc107;"></i>
                    <h3 style="color: var(--primary-green);"><?php echo $stats['avg_rating'] ?: 'N/A'; ?></h3>
                    <p class="mb-0">Average Rating</p>
                    <small class="text-muted">(<?php echo $stats['total_ratings']; ?> ratings)</small>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-4">
            <div class="card fade-in-up h-100">
                <div class="card-body text-center">
                    <i class="fas fa-percentage fa-3x mb-3" style="color: var(--primary-green);"></i>
                    <h3 style="color: var(--primary-green);">
                        <?php
                        $success_rate = $stats['total_appointments'] > 0
                            ? round(($stats['completed'] / $stats['total_appointments']) * 100, 1)
                            : 0;
                        echo $success_rate . '%';
                        ?>
                    </h3>
                    <p class="mb-0">Appointment Completion Rate</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Appointments -->
    <div class="row">
        <div class="col-12">
            <div class="card fade-in-up">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-clock"></i> Recent Appointments</h5>
                </div>
                <div class="card-body">
                    <?php if ($recent_appointments->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Branch</th>
                                    <th>Service</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($appointment = $recent_appointments->fetch_assoc()):
                                    $status_class = 'status-' . $appointment['status'];
                                    $status_text = $appointment['status'] === 'upcoming' ? 'Upcoming' : ($appointment['status'] === 'completed' ? 'Completed' : 'Cancelled');
                                ?>
                                <tr>
                                    <td><i class="fas fa-user"></i> <?php echo $appointment['user_name']; ?></td>
                                    <td><i class="fas fa-building"></i> <?php echo $appointment['branch_name'] ?? 'N/A'; ?></td>
                                    <td><?php echo $appointment['service']; ?></td>
                                    <td><?php echo date('Y/m/d', strtotime($appointment['date'])); ?></td>
                                    <td><?php echo date('h:i A', strtotime($appointment['time'])); ?></td>
                                    <td><span class="<?php echo $status_class; ?>"><?php echo $status_text; ?></span></td>
                                    <td>
                                        <?php if ($appointment['status'] === 'upcoming'): ?>
                                        <a href="?complete_appointment=<?php echo $appointment['id']; ?>" class="btn btn-sm btn-outline-success" onclick="return confirm('Are you sure you want to mark this appointment as completed?')">
                                            <i class="fas fa-check-circle"></i> Mark Complete
                                        </a>
                                        <a href="?cancel_appointment=<?php echo $appointment['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to cancel this appointment?')">
                                            <i class="fas fa-ban"></i> Cancel
                                        </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <p class="text-center text-muted">No appointments yet</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
// Appointment status chart
var ctx1 = document.getElementById('statusChart').getContext('2d');
var statusChart = new Chart(ctx1, {
    type: 'doughnut',
    data: {
        labels: ['Upcoming', 'Completed', 'Cancelled'],
        datasets: [{
            data: [<?php echo $stats['upcoming']; ?>, <?php echo $stats['completed']; ?>, <?php echo $stats['cancelled']; ?>],
            backgroundColor: ['#006e3d', '#6c757d', '#dc3545']
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Most requested services chart
var services = [];
var counts = [];
<?php
$popular_services->data_seek(0);
while ($service = $popular_services->fetch_assoc()) {
    echo "services.push('" . $service['service'] . "');\n";
    echo "counts.push(" . $service['count'] . ");\n";
}
?>

var ctx2 = document.getElementById('servicesChart').getContext('2d');
var servicesChart = new Chart(ctx2, {
    type: 'bar',
    data: {
        labels: services,
        datasets: [{
            label: 'Number of Appointments',
            data: counts,
            backgroundColor: '#006e3d'
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        },
        plugins: {
            legend: {
                display: false
            }
        }
    }
});
</script>

<?php require_once '../includes/footer.php'; ?>
