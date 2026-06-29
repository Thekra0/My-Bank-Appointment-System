<?php
$pageTitle = 'Ratings';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

// Protect page
requireLogin();
$user_id = $_SESSION['user_id'];

// Add new rating
if (isset($_POST['submit_rating'])) {
    $appointment_id = (int)$_POST['appointment_id'];
    $rating = (int)$_POST['rating'];
    $feedback = cleanInput($_POST['feedback']);

    // Verify appointment belongs to user and is completed
    $check_query = "SELECT * FROM appointments WHERE id = ? AND user_id = ? AND status = 'completed'";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param('ii', $appointment_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Check for existing rating
        $check_rating = "SELECT id FROM ratings WHERE appointment_id = ? AND user_id = ?";
        $stmt = $conn->prepare($check_rating);
        $stmt->bind_param('ii', $appointment_id, $user_id);
        $stmt->execute();
        $rating_result = $stmt->get_result();

        if ($rating_result->num_rows === 0) {
            $insert_query = "INSERT INTO ratings (user_id, appointment_id, rating, feedback) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_query);
            $stmt->bind_param('iiis', $user_id, $appointment_id, $rating, $feedback);

            if ($stmt->execute()) {
                setMessage('Rating added successfully!', 'success');
            } else {
                setMessage('An error occurred while adding the rating', 'error');
            }
        } else {
            setMessage('You have already rated this appointment', 'error');
        }
    } else {
        setMessage('Appointment is not eligible for rating', 'error');
    }
    header('Location: rating.php');
    exit();
}

// Get completed appointments without ratings
$query = "SELECT a.* FROM appointments a
          LEFT JOIN ratings r ON a.id = r.appointment_id
          WHERE a.user_id = ? AND a.status = 'completed' AND r.id IS NULL
          ORDER BY a.date DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$appointments_to_rate = $stmt->get_result();

// Get previous ratings
$query = "SELECT r.*, a.service, a.date, a.time
          FROM ratings r
          JOIN appointments a ON r.appointment_id = a.id
          WHERE r.user_id = ?
          ORDER BY r.created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$my_ratings = $stmt->get_result();

// Get all ratings (public display)
$all_ratings_query = "SELECT r.*, u.name as user_name, a.service
                      FROM ratings r
                      JOIN users u ON r.user_id = u.id
                      JOIN appointments a ON r.appointment_id = a.id
                      ORDER BY r.created_at DESC
                      LIMIT 20";
$all_ratings = $conn->query($all_ratings_query);
?>

<div class="container my-5">
    <div class="row mb-4">
        <div class="col-12">
            <h2 style="color: var(--primary-green);"><i class="fas fa-star"></i> Rating System</h2>
            <p class="text-muted">Rate your experience using our services</p>
        </div>
    </div>

    <!-- Appointments Awaiting Rating -->
    <?php if ($appointments_to_rate->num_rows > 0): ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="card fade-in-up">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-star-half-alt"></i> Appointments Awaiting Your Rating</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php while ($appointment = $appointments_to_rate->fetch_assoc()): ?>
                        <div class="col-md-6 mb-3">
                            <div class="card border-warning">
                                <div class="card-body">
                                    <h5 class="card-title" style="color: var(--primary-green);">
                                        <i class="fas fa-briefcase"></i> <?php echo $appointment['service']; ?>
                                    </h5>
                                    <p class="card-text">
                                        <i class="fas fa-calendar"></i> <?php echo date('Y/m/d', strtotime($appointment['date'])); ?>
                                        <i class="fas fa-clock ms-3"></i> <?php echo date('h:i A', strtotime($appointment['time'])); ?>
                                    </p>
                                    <button class="btn btn-primary btn-sm" onclick="openRatingModal(<?php echo $appointment['id']; ?>, '<?php echo $appointment['service']; ?>')">
                                        <i class="fas fa-star"></i> Add Your Rating Now
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="row">
        <!-- My Ratings -->
        <div class="col-md-6 mb-4">
            <div class="card fade-in-up">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-user-check"></i> My Ratings</h5>
                </div>
                <div class="card-body" style="max-height: 600px; overflow-y: auto;">
                    <?php if ($my_ratings->num_rows > 0): ?>
                        <?php while ($rating = $my_ratings->fetch_assoc()): ?>
                        <div class="card mb-3 border-start border-4" style="border-color: var(--primary-green) !important;">
                            <div class="card-body">
                                <h6 style="color: var(--primary-green);"><?php echo $rating['service']; ?></h6>
                                <div class="rating-stars mb-2">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="fa<?php echo $i <= $rating['rating'] ? 's' : 'r'; ?> fa-star"></i>
                                    <?php endfor; ?>
                                    <span class="ms-2">(<?php echo $rating['rating']; ?>/5)</span>
                                </div>
                                <?php if ($rating['feedback']): ?>
                                <p class="mb-0"><i class="fas fa-comment"></i> <?php echo $rating['feedback']; ?></p>
                                <?php endif; ?>
                                <small class="text-muted">
                                    <i class="fas fa-clock"></i> <?php echo date('Y/m/d h:i A', strtotime($rating['created_at'])); ?>
                                </small>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-star-half-alt fa-3x text-muted mb-3"></i>
                            <p class="text-muted">You haven't added any ratings yet</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- All Ratings -->
        <div class="col-md-6 mb-4">
            <div class="card fade-in-up">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-comments"></i> Customer Reviews</h5>
                </div>
                <div class="card-body" style="max-height: 600px; overflow-y: auto;">
                    <?php if ($all_ratings->num_rows > 0): ?>
                        <?php while ($rating = $all_ratings->fetch_assoc()): ?>
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 style="color: var(--primary-green);">
                                        <i class="fas fa-user-circle"></i> <?php echo $rating['user_name']; ?>
                                    </h6>
                                    <small class="text-muted"><?php echo $rating['service']; ?></small>
                                </div>
                                <div class="rating-stars mb-2">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="fa<?php echo $i <= $rating['rating'] ? 's' : 'r'; ?> fa-star"></i>
                                    <?php endfor; ?>
                                </div>
                                <?php if ($rating['feedback']): ?>
                                <p class="mb-0"><?php echo $rating['feedback']; ?></p>
                                <?php endif; ?>
                                <small class="text-muted">
                                    <i class="fas fa-calendar"></i> <?php echo date('Y/m/d', strtotime($rating['created_at'])); ?>
                                </small>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No ratings yet</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Rating Modal -->
<div class="modal fade" id="ratingModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: var(--primary-green); color: white;">
                <h5 class="modal-title"><i class="fas fa-star"></i> Add Rating</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" name="appointment_id" id="rating_appointment_id">

                    <div class="mb-3">
                        <label class="form-label">Service</label>
                        <p id="rating_service" class="fw-bold" style="color: var(--primary-green);"></p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Rating</label>
                        <div class="rating-stars" style="font-size: 2rem;">
                            <i class="far fa-star" data-rating="1" onclick="setRating(1)"></i>
                            <i class="far fa-star" data-rating="2" onclick="setRating(2)"></i>
                            <i class="far fa-star" data-rating="3" onclick="setRating(3)"></i>
                            <i class="far fa-star" data-rating="4" onclick="setRating(4)"></i>
                            <i class="far fa-star" data-rating="5" onclick="setRating(5)"></i>
                        </div>
                        <input type="hidden" name="rating" id="rating_value" required>
                    </div>

                    <div class="mb-3">
                        <label for="feedback" class="form-label">Your Comments (Optional)</label>
                        <textarea class="form-control" id="feedback" name="feedback" rows="4" placeholder="Share your experience..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="submit_rating" class="btn btn-primary">
                        <i class="fas fa-check"></i> Submit Rating
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openRatingModal(appointmentId, service) {
    document.getElementById('rating_appointment_id').value = appointmentId;
    document.getElementById('rating_service').textContent = service;
    document.getElementById('rating_value').value = '';

    // Reset stars
    var stars = document.querySelectorAll('.rating-stars i');
    stars.forEach(star => {
        star.classList.remove('fas');
        star.classList.add('far');
    });

    var modal = new bootstrap.Modal(document.getElementById('ratingModal'));
    modal.show();
}

function setRating(rating) {
    document.getElementById('rating_value').value = rating;

    var stars = document.querySelectorAll('#ratingModal .rating-stars i');
    stars.forEach((star, index) => {
        if (index < rating) {
            star.classList.remove('far');
            star.classList.add('fas');
        } else {
            star.classList.remove('fas');
            star.classList.add('far');
        }
    });
}
</script>

<?php require_once '../includes/footer.php'; ?>
