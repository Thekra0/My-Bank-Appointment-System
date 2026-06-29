<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="/index.php">
            <img src="/assets/images/bank-logo.png" alt="Bank Logo" class="logo" onerror="this.style.display='none'">
            <span>Bank Appointments</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <?php if (isLoggedIn()): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/index.php">
                            <i class="fas fa-home"></i> Home
                        </a>
                    </li>
                    <?php if (!isAdmin()): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/pages/appointments.php">
                            <i class="fas fa-calendar-alt"></i> My Appointments
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/pages/rating.php">
                            <i class="fas fa-star"></i> Ratings
                        </a>
                    </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/pages/branches.php">
                            <i class="fas fa-map-marker-alt"></i> Branches
                        </a>
                    </li>
                    <?php if (isAdmin()): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/pages/dashboard.php">
                            <i class="fas fa-chart-bar"></i> Dashboard
                        </a>
                    </li>
                    <?php endif; ?>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/index.php">
                            <i class="fas fa-home"></i> Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/pages/branches.php">
                            <i class="fas fa-map-marker-alt"></i> Branches
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
            <ul class="navbar-nav">
                <?php if (isLoggedIn()): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle"></i> <?php echo $_SESSION['user_name']; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="/auth/logout.php">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/auth/login.php">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/auth/register.php">
                            <i class="fas fa-user-plus"></i> Register
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
