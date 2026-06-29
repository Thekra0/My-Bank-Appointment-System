<?php
$pageTitle = 'Branches';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

// Add new branch (admin only)
if (isset($_POST['add_branch']) && isAdmin()) {
    $name = cleanInput($_POST['name']);
    $address = cleanInput($_POST['address']);
    $map_embed = cleanInput($_POST['map_embed']);

    if (!empty($name) && !empty($address)) {
        $query = "INSERT INTO branches (name, address, map_embed) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('sss', $name, $address, $map_embed);

        if ($stmt->execute()) {
            setMessage('Branch added successfully!', 'success');
        } else {
            setMessage('An error occurred while adding the branch', 'error');
        }
    } else {
        setMessage('Please fill in all required fields', 'error');
    }
    header('Location: branches.php');
    exit();
}

// Update branch (admin only)
if (isset($_POST['update_branch']) && isAdmin()) {
    $branch_id = (int)$_POST['branch_id'];
    $name = cleanInput($_POST['name']);
    $address = cleanInput($_POST['address']);
    $map_embed = cleanInput($_POST['map_embed']);

    if (!empty($name) && !empty($address)) {
        $query = "UPDATE branches SET name = ?, address = ?, map_embed = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('sssi', $name, $address, $map_embed, $branch_id);

        if ($stmt->execute()) {
            setMessage('Branch updated successfully!', 'success');
        } else {
            setMessage('An error occurred while updating the branch', 'error');
        }
    } else {
        setMessage('Please fill in all required fields', 'error');
    }
    header('Location: branches.php');
    exit();
}

// Delete branch (admin only)
if (isset($_GET['delete']) && is_numeric($_GET['delete']) && isAdmin()) {
    $branch_id = (int)$_GET['delete'];

    // Check if branch has appointments
    $check_query = "SELECT COUNT(*) as count FROM appointments WHERE branch_id = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param('i', $branch_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $count = $result->fetch_assoc()['count'];

    if ($count > 0) {
        setMessage('Cannot delete branch with existing appointments', 'error');
    } else {
        $query = "DELETE FROM branches WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $branch_id);

        if ($stmt->execute()) {
            setMessage('Branch deleted successfully!', 'success');
        } else {
            setMessage('An error occurred while deleting the branch', 'error');
        }
    }
    header('Location: branches.php');
    exit();
}

// Get all branches
$query = "SELECT * FROM branches ORDER BY id";
$branches = $conn->query($query);
?>

<div class="container my-5">
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h2 style="color: var(--primary-green);"><i class="fas fa-map-marker-alt"></i> Our Branches in the Kingdom</h2>
            <p class="text-muted">Choose the nearest branch to you</p>
            <?php if (isAdmin()): ?>
            <button class="btn btn-primary mt-2" onclick="showAddBranchModal()">
                <i class="fas fa-plus-circle"></i> Add New Branch
            </button>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($branches->num_rows > 0): ?>
        <?php
        $counter = 0;
        while ($branch = $branches->fetch_assoc()):
            $counter++;
        ?>
        <div class="row mb-5 fade-in-up">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-building"></i> <?php echo $branch['name']; ?>
                        </h4>
                        <?php if (isAdmin()): ?>
                        <div>
                            <button class="btn btn-sm btn-outline-primary" onclick="editBranch(<?php echo htmlspecialchars(json_encode($branch)); ?>)">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <a href="?delete=<?php echo $branch['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this branch?')">
                                <i class="fas fa-trash"></i> Delete
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <h5 style="color: var(--primary-green);">
                                    <i class="fas fa-map-marked-alt"></i> Address
                                </h5>
                                <p class="mb-3"><?php echo $branch['address']; ?></p>

                                <h5 style="color: var(--primary-green);">
                                    <i class="fas fa-info-circle"></i> Contact Information
                                </h5>
                                <p class="mb-1"><i class="fas fa-phone"></i> 920000000</p>
                                <p class="mb-1"><i class="fas fa-envelope"></i> info@bank.com</p>

                                <h5 class="mt-3" style="color: var(--primary-green);">
                                    <i class="fas fa-clock"></i> Business Hours
                                </h5>
                                <p class="mb-1">Saturday - Wednesday: 8:00 AM - 4:00 PM</p>
                                <p class="mb-1">Thursday: 8:00 AM - 12:00 PM</p>
                                <p class="mb-0">Friday: Closed</p>

                                <?php if (isLoggedIn()): ?>
                                <div class="mt-3">
                                    <a href="/pages/appointments.php" class="btn btn-primary">
                                        <i class="fas fa-calendar-plus"></i> Book Your Appointment Now
                                    </a>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-8">
                                <div class="map-container">
                                    <?php if (!empty($branch['map_embed'])): ?>
                                        <iframe
                                            src="<?php echo $branch['map_embed']; ?>"
                                            allowfullscreen=""
                                            loading="lazy"
                                            referrerpolicy="no-referrer-when-downgrade">
                                        </iframe>
                                    <?php else: ?>
                                        <div class="d-flex align-items-center justify-content-center h-100 bg-light">
                                            <div class="text-center">
                                                <i class="fas fa-map-marked-alt fa-5x text-muted mb-3"></i>
                                                <p class="text-muted">Map not currently available</p>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-building fa-4x text-muted mb-3"></i>
                        <p class="text-muted">No branches currently available</p>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Additional Information -->
    <div class="row mt-5">
        <div class="col-md-4 mb-3">
            <div class="card fade-in-up h-100">
                <div class="card-body text-center">
                    <div class="icon-box">
                        <i class="fas fa-parking"></i>
                    </div>
                    <h5 style="color: var(--primary-green);">Free Parking</h5>
                    <p class="text-muted">Spacious and free parking available at all branches</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card fade-in-up h-100">
                <div class="card-body text-center">
                    <div class="icon-box">
                        <i class="fas fa-wheelchair"></i>
                    </div>
                    <h5 style="color: var(--primary-green);">Accessibility Services</h5>
                    <p class="text-muted">All our branches are equipped to serve people with special needs</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card fade-in-up h-100">
                <div class="card-body text-center">
                    <div class="icon-box">
                        <i class="fas fa-wifi"></i>
                    </div>
                    <h5 style="color: var(--primary-green);">Free Wi-Fi</h5>
                    <p class="text-muted">Free Wi-Fi service for all customers</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Visit Tips -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card fade-in-up" style="background: linear-gradient(135deg, var(--primary-green) 0%, var(--light-green) 100%); color: white;">
                <div class="card-body">
                    <h4 class="mb-3"><i class="fas fa-lightbulb"></i> Tips Before Your Visit</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <ul>
                                <li>Book your appointment in advance to avoid waiting</li>
                                <li>Bring your national ID card</li>
                                <li>Arrive 10 minutes before your appointment</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul>
                                <li>Prepare all required documents for the service</li>
                                <li>You can cancel or modify your appointment from your account</li>
                                <li>For inquiries: Call 920000000</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Branch Modal (Admin Only) -->
<?php if (isAdmin()): ?>
<div class="modal fade" id="addBranchModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background-color: var(--primary-green); color: white;">
                <h5 class="modal-title"><i class="fas fa-plus-circle"></i> Add New Branch</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="add_name" class="form-label">Branch Name</label>
                        <input type="text" class="form-control" id="add_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="add_address" class="form-label">Address</label>
                        <textarea class="form-control" id="add_address" name="address" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="add_map_embed" class="form-label">Google Maps Embed URL (Optional)</label>
                        <textarea class="form-control" id="add_map_embed" name="map_embed" rows="2" placeholder="https://www.google.com/maps/embed?pb=..."></textarea>
                        <small class="text-muted">Get embed URL from Google Maps by clicking Share > Embed a map</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_branch" class="btn btn-primary">
                        <i class="fas fa-save"></i> Add Branch
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Branch Modal (Admin Only) -->
<div class="modal fade" id="editBranchModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background-color: var(--primary-green); color: white;">
                <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Branch</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" name="branch_id" id="edit_branch_id">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Branch Name</label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_address" class="form-label">Address</label>
                        <textarea class="form-control" id="edit_address" name="address" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit_map_embed" class="form-label">Google Maps Embed URL (Optional)</label>
                        <textarea class="form-control" id="edit_map_embed" name="map_embed" rows="2"></textarea>
                        <small class="text-muted">Get embed URL from Google Maps by clicking Share > Embed a map</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="update_branch" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showAddBranchModal() {
    var modal = new bootstrap.Modal(document.getElementById('addBranchModal'));
    modal.show();
}

function editBranch(branch) {
    document.getElementById('edit_branch_id').value = branch.id;
    document.getElementById('edit_name').value = branch.name;
    document.getElementById('edit_address').value = branch.address;
    document.getElementById('edit_map_embed').value = branch.map_embed || '';

    var modal = new bootstrap.Modal(document.getElementById('editBranchModal'));
    modal.show();
}
</script>
<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>
