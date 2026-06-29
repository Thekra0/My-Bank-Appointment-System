<?php
require_once __DIR__ . '/../config/db.php';

// Automatically mark past appointments as completed
autoCompletePastAppointments();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?>Bank Appointment System</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="/assets/css/style.css">

    <!-- SweetAlert2 for notifications -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>
<body>
    <?php
    // Display notification messages
    $msg = getMessage();
    if ($msg):
    ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: '<?php echo $msg['type'] === 'success' ? 'success' : 'error'; ?>',
                title: '<?php echo $msg['type'] === 'success' ? 'Success' : 'Error'; ?>',
                text: '<?php echo $msg['message']; ?>',
                confirmButtonColor: '#006e3d',
                timer: 3000
            });
        });
    </script>
    <?php endif; ?>
