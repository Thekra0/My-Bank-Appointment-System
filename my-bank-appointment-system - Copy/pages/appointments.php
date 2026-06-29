<?php
$pageTitle = 'My Appointments';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

// Protect page - login required
requireLogin();

// Prevent admins from accessing this page
if (isAdmin()) {
    header('Location: /pages/dashboard.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Add new appointment
if (isset($_POST['add_appointment'])) {
    $branch_id = (int)$_POST['branch_id'];
    $service = cleanInput($_POST['service']);
    $date = cleanInput($_POST['date']);
    $time = cleanInput($_POST['time']);

    if (!empty($branch_id) && !empty($service) && !empty($date) && !empty($time)) {
        // Verify date is in the future
        if ($date >= date('Y-m-d')) {
            $query = "INSERT INTO appointments (user_id, branch_id, service, date, time, status) VALUES (?, ?, ?, ?, ?, 'upcoming')";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('iisss', $user_id, $branch_id, $service, $date, $time);

            if ($stmt->execute()) {
                setMessage('Appointment booked successfully!', 'success');
            } else {
                setMessage('An error occurred while booking the appointment', 'error');
            }
        } else {
            setMessage('Date must be in the future', 'error');
        }
    } else {
        setMessage('Please fill in all fields', 'error');
    }
    header('Location: appointments.php');
    exit();
}

// Update appointment
if (isset($_POST['update_appointment'])) {
    $appointment_id = (int)$_POST['appointment_id'];
    $branch_id = (int)$_POST['branch_id'];
    $service = cleanInput($_POST['service']);
    $date = cleanInput($_POST['date']);
    $time = cleanInput($_POST['time']);

    if (!empty($branch_id) && !empty($service) && !empty($date) && !empty($time)) {
        $query = "UPDATE appointments SET branch_id = ?, service = ?, date = ?, time = ? WHERE id = ? AND user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('isssii', $branch_id, $service, $date, $time, $appointment_id, $user_id);

        if ($stmt->execute()) {
            setMessage('Appointment updated successfully!', 'success');
        } else {
            setMessage('An error occurred while updating the appointment', 'error');
        }
    }
    header('Location: appointments.php');
    exit();
}

// Cancel appointment
if (isset($_GET['cancel']) && is_numeric($_GET['cancel'])) {
    $appointment_id = (int)$_GET['cancel'];

    $query = "UPDATE appointments SET status = 'cancelled' WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ii', $appointment_id, $user_id);

    if ($stmt->execute()) {
        setMessage('Appointment cancelled', 'success');
    } else {
        setMessage('An error occurred during cancellation', 'error');
    }
    header('Location: appointments.php');
    exit();
}

// Delete appointment
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $appointment_id = (int)$_GET['delete'];

    $query = "DELETE FROM appointments WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ii', $appointment_id, $user_id);

    if ($stmt->execute()) {
        setMessage('Appointment deleted', 'success');
    } else {
        setMessage('An error occurred during deletion', 'error');
    }
    header('Location: appointments.php');
    exit();
}

// Get all branches for dropdown
$branches_query = "SELECT * FROM branches ORDER BY name";
$branches = $conn->query($branches_query);

// Get all appointments with branch info
$query = "SELECT a.*, b.name as branch_name FROM appointments a
          LEFT JOIN branches b ON a.branch_id = b.id
          WHERE a.user_id = ? ORDER BY a.date DESC, a.time DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$appointments = $stmt->get_result();
?>

<div class="container my-5">
    <div class="row">
        <div class="col-12 mb-4">
            <h2 style="color: var(--primary-green);"><i class="fas fa-calendar-alt"></i> Appointment Management</h2>
        </div>
    </div>

    <!-- Add New Appointment Form -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card fade-in-up">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-plus-circle"></i> Book New Appointment</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="branch_id" class="form-label">Branch</label>
                                <select class="form-control" id="branch_id" name="branch_id" required>
                                    <option value="">Choose Branch...</option>
                                    <?php
                                    $branches->data_seek(0);
                                    while ($branch = $branches->fetch_assoc()):
                                    ?>
                                    <option value="<?php echo $branch['id']; ?>"><?php echo $branch['name']; ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="service" class="form-label">Service Type</label>
                                <select class="form-control" id="service" name="service" required>
                                    <option value="">Choose Service...</option>
                                    <option value="Open New Account">Open New Account</option>
                                    <option value="Loan Inquiry">Loan Inquiry</option>
                                    <option value="Credit Card Application">Credit Card Application</option>
                                    <option value="Update Account Information">Update Account Information</option>
                                    <option value="Financial Consultation">Financial Consultation</option>
                                    <option value="Other Services">Other Services</option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="date" class="form-label">Date</label>
                                <input type="date" class="form-control" id="date" name="date" min="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="time" class="form-label">Time</label>
                                <input type="time" class="form-control" id="time" name="time" min="08:00" max="16:00" required>
                                <small class="text-muted">8 AM - 4 PM</small>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label d-block">&nbsp;</label>
                                <button type="submit" name="add_appointment" class="btn btn-primary w-100">
                                    <i class="fas fa-check"></i> Book
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Appointments List -->
    <div class="row">
        <div class="col-12">
            <div class="card fade-in-up">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-list"></i> All Appointments</h5>
                </div>
                <div class="card-body">
                    <?php if ($appointments->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Branch</th>
                                    <th>Service</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $counter = 1;
                                while ($appointment = $appointments->fetch_assoc()):
                                    $status_class = 'status-' . $appointment['status'];
                                    $status_text = $appointment['status'] === 'upcoming' ? 'Upcoming' : ($appointment['status'] === 'completed' ? 'Completed' : 'Cancelled');
                                ?>
                                <tr>
                                    <td><?php echo $counter++; ?></td>
                                    <td><i class="fas fa-building"></i> <?php echo $appointment['branch_name'] ?? 'N/A'; ?></td>
                                    <td><i class="fas fa-briefcase"></i> <?php echo $appointment['service']; ?></td>
                                    <td><i class="fas fa-calendar"></i> <?php echo date('Y/m/d', strtotime($appointment['date'])); ?></td>
                                    <td><i class="fas fa-clock"></i> <?php echo date('h:i A', strtotime($appointment['time'])); ?></td>
                                    <td><span class="<?php echo $status_class; ?>"><?php echo $status_text; ?></span></td>
                                    <td>
                                        <?php if ($appointment['status'] === 'upcoming'): ?>
                                        <button class="btn btn-sm btn-outline-primary" onclick="editAppointment(<?php echo htmlspecialchars(json_encode($appointment)); ?>)">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <a href="?cancel=<?php echo $appointment['id']; ?>" class="btn btn-sm btn-outline-warning" onclick="return confirm('Do you want to cancel this appointment?')">
                                            <i class="fas fa-ban"></i> Cancel
                                        </a>
                                        <?php endif; ?>
                                        <button class="btn btn-sm btn-outline-success no-print" onclick="printAppointment(<?php echo htmlspecialchars(json_encode($appointment)); ?>)">
                                            <i class="fas fa-print"></i> Print
                                        </button>
                                        <a href="?delete=<?php echo $appointment['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Do you want to permanently delete this appointment?')">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                        <p class="text-muted">No appointments booked</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Appointment Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: var(--primary-green); color: white;">
                <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Appointment</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" name="appointment_id" id="edit_appointment_id">
                    <div class="mb-3">
                        <label for="edit_branch_id" class="form-label">Branch</label>
                        <select class="form-control" id="edit_branch_id" name="branch_id" required>
                            <?php
                            $branches->data_seek(0);
                            while ($branch = $branches->fetch_assoc()):
                            ?>
                            <option value="<?php echo $branch['id']; ?>"><?php echo $branch['name']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_service" class="form-label">Service Type</label>
                        <select class="form-control" id="edit_service" name="service" required>
                            <option value="Open New Account">Open New Account</option>
                            <option value="Loan Inquiry">Loan Inquiry</option>
                            <option value="Credit Card Application">Credit Card Application</option>
                            <option value="Update Account Information">Update Account Information</option>
                            <option value="Financial Consultation">Financial Consultation</option>
                            <option value="Other Services">Other Services</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_date" class="form-label">Date</label>
                        <input type="date" class="form-control" id="edit_date" name="date" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_time" class="form-label">Time</label>
                        <input type="time" class="form-control" id="edit_time" name="time" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="update_appointment" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editAppointment(appointment) {
    document.getElementById('edit_appointment_id').value = appointment.id;
    document.getElementById('edit_branch_id').value = appointment.branch_id;
    document.getElementById('edit_service').value = appointment.service;
    document.getElementById('edit_date').value = appointment.date;
    document.getElementById('edit_time').value = appointment.time;

    var editModal = new bootstrap.Modal(document.getElementById('editModal'));
    editModal.show();
}

function printAppointment(appointment) {
    var userName = '<?php echo $_SESSION['user_name']; ?>';
    var printWindow = window.open('', '', 'height=600,width=800');
    printWindow.document.write('<html><head><title>Appointment Receipt</title>');
    printWindow.document.write('<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">');
    printWindow.document.write('<style>body{font-family:"Cairo",sans-serif;padding:40px;text-align:center;}');
    printWindow.document.write('.header{background:#006e3d;color:white;padding:20px;border-radius:10px;margin-bottom:30px;}');
    printWindow.document.write('.info{text-align:left;line-height:2;background:#f8f9fa;padding:20px;border-radius:10px;}');
    printWindow.document.write('.info strong{color:#006e3d;}</style></head><body>');
    printWindow.document.write('<div class="header"><h1>Appointment Booking Receipt</h1><p>Bank Appointment System</p></div>');
    printWindow.document.write('<div class="info">');
    printWindow.document.write('<p><strong>Name:</strong> ' + userName + '</p>');
    printWindow.document.write('<p><strong>Service:</strong> ' + appointment.service + '</p>');
    printWindow.document.write('<p><strong>Date:</strong> ' + appointment.date + '</p>');
    printWindow.document.write('<p><strong>Time:</strong> ' + appointment.time + '</p>');
    printWindow.document.write('<p><strong>Appointment Number:</strong> #' + appointment.id + '</p>');
    printWindow.document.write('</div>');
    printWindow.document.write('<p style="margin-top:30px;color:#666;">Please arrive 10 minutes before your appointment</p>');
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.print();
}
</script>

<?php require_once '../includes/footer.php'; ?>
