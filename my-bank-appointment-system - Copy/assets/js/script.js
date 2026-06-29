// Bank Appointment System - JavaScript

// Effects on page load
document.addEventListener('DOMContentLoaded', function() {
    // Apply fade-in animation
    const fadeElements = document.querySelectorAll('.fade-in-up');
    fadeElements.forEach((element, index) => {
        setTimeout(() => {
            element.style.opacity = '0';
            element.style.transform = 'translateY(30px)';
            element.style.transition = 'all 0.6s ease-out';

            setTimeout(() => {
                element.style.opacity = '1';
                element.style.transform = 'translateY(0)';
            }, 100);
        }, index * 100);
    });

    // Enable tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Add card hover effects
    const cards = document.querySelectorAll('.card');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
        });
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
});

// Function to show notifications
function showNotification(title, message, type = 'success') {
    const icon = type === 'success' ? 'success' : 'error';
    Swal.fire({
        icon: icon,
        title: title,
        text: message,
        confirmButtonColor: '#006e3d',
        timer: 3000,
        timerProgressBar: true
    });
}

// Confirmation function before deletion
function confirmDelete(message = 'Are you sure you want to delete?') {
    return new Promise((resolve) => {
        Swal.fire({
            title: 'Confirm',
            text: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            resolve(result.isConfirmed);
        });
    });
}

// Countdown timer function for appointments
function startAppointmentCountdown(appointmentDate, appointmentTime, elementId) {
    const countdownElement = document.getElementById(elementId);
    if (!countdownElement) return;

    const targetDate = new Date(appointmentDate + ' ' + appointmentTime);

    function updateCountdown() {
        const now = new Date();
        const difference = targetDate - now;

        if (difference <= 0) {
            countdownElement.innerHTML = '<span class="badge bg-success">Time\'s up!</span>';
            return;
        }

        const days = Math.floor(difference / (1000 * 60 * 60 * 24));
        const hours = Math.floor((difference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((difference % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((difference % (1000 * 60)) / 1000);

        let countdownHTML = '<div class="d-flex gap-2 justify-content-center">';

        if (days > 0) {
            countdownHTML += `<div class="text-center"><strong>${days}</strong><br><small>day(s)</small></div>`;
        }
        if (hours > 0 || days > 0) {
            countdownHTML += `<div class="text-center"><strong>${hours}</strong><br><small>hour(s)</small></div>`;
        }
        countdownHTML += `<div class="text-center"><strong>${minutes}</strong><br><small>min(s)</small></div>`;
        countdownHTML += `<div class="text-center"><strong>${seconds}</strong><br><small>sec(s)</small></div>`;
        countdownHTML += '</div>';

        countdownElement.innerHTML = countdownHTML;
    }

    updateCountdown();
    setInterval(updateCountdown, 1000);
}

// Form validation function
function validateAppointmentForm(formData) {
    const errors = [];

    if (!formData.service || formData.service.trim() === '') {
        errors.push('Please select a service type');
    }

    if (!formData.date) {
        errors.push('Please select a date');
    } else {
        const selectedDate = new Date(formData.date);
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        if (selectedDate < today) {
            errors.push('Cannot book an appointment in the past');
        }
    }

    if (!formData.time) {
        errors.push('Please select a time');
    } else {
        const [hours, minutes] = formData.time.split(':');
        const timeInMinutes = parseInt(hours) * 60 + parseInt(minutes);
        const startTime = 8 * 60; // 8:00 AM
        const endTime = 16 * 60;   // 4:00 PM

        if (timeInMinutes < startTime || timeInMinutes > endTime) {
            errors.push('Business hours are from 8:00 AM to 4:00 PM');
        }
    }

    return errors;
}

// Date formatting function
function formatDate(dateString) {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', options);
}

// Time formatting function
function formatTime(timeString) {
    const [hours, minutes] = timeString.split(':');
    const hour = parseInt(hours);
    const ampm = hour >= 12 ? 'PM' : 'AM';
    const displayHour = hour > 12 ? hour - 12 : hour;
    return `${displayHour}:${minutes} ${ampm}`;
}

// Function to search in tables
function searchTable(inputId, tableId) {
    const input = document.getElementById(inputId);
    const table = document.getElementById(tableId);

    if (!input || !table) return;

    input.addEventListener('keyup', function() {
        const filter = this.value.toLowerCase();
        const rows = table.getElementsByTagName('tr');

        for (let i = 1; i < rows.length; i++) {
            const row = rows[i];
            const cells = row.getElementsByTagName('td');
            let found = false;

            for (let j = 0; j < cells.length; j++) {
                const cell = cells[j];
                if (cell.textContent.toLowerCase().indexOf(filter) > -1) {
                    found = true;
                    break;
                }
            }

            row.style.display = found ? '' : 'none';
        }
    });
}

// Function to check upcoming appointments in real-time
function checkUpcomingAppointments() {
    // Can be developed to fetch upcoming appointments via AJAX
    const now = new Date();
    const tomorrow = new Date(now.getTime() + 24 * 60 * 60 * 1000);

    // AJAX code can be added here to check upcoming appointments
    // and show alerts to the user
}

// Function to print specific information
function printElement(elementId) {
    const element = document.getElementById(elementId);
    if (!element) return;

    const printWindow = window.open('', '', 'height=600,width=800');
    printWindow.document.write('<html><head><title>Print</title>');
    printWindow.document.write('<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">');
    printWindow.document.write('<style>body{font-family:"Cairo",sans-serif;padding:20px;}</style>');
    printWindow.document.write('</head><body>');
    printWindow.document.write(element.innerHTML);
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.print();
}

// Function to toggle mobile menu
function toggleMobileMenu() {
    const navbarToggler = document.querySelector('.navbar-toggler');
    if (navbarToggler) {
        navbarToggler.addEventListener('click', function() {
            this.classList.toggle('active');
        });
    }
}

// Activate functions on page load
window.addEventListener('load', function() {
    toggleMobileMenu();

    // Check for upcoming appointments every 5 minutes
    setInterval(checkUpcomingAppointments, 5 * 60 * 1000);
});

// Function to show loading effect
function showLoader() {
    Swal.fire({
        title: 'Loading...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
}

function hideLoader() {
    Swal.close();
}

// Function to check internet connection
window.addEventListener('online', function() {
    showNotification('Connected', 'Internet connection restored', 'success');
});

window.addEventListener('offline', function() {
    showNotification('Disconnected', 'No internet connection', 'error');
});

// Function to save data locally (LocalStorage)
function saveToLocalStorage(key, value) {
    try {
        localStorage.setItem(key, JSON.stringify(value));
        return true;
    } catch (e) {
        console.error('Error saving data:', e);
        return false;
    }
}

function getFromLocalStorage(key) {
    try {
        const item = localStorage.getItem(key);
        return item ? JSON.parse(item) : null;
    } catch (e) {
        console.error('Error reading data:', e);
        return null;
    }
}

// Prevent form submission when pressing Enter in search fields
document.addEventListener('DOMContentLoaded', function() {
    const searchInputs = document.querySelectorAll('input[type="search"]');
    searchInputs.forEach(input => {
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
            }
        });
    });
});

// Function to show welcome message on first visit
function showWelcomeMessage() {
    const hasVisited = getFromLocalStorage('hasVisited');

    if (!hasVisited) {
        Swal.fire({
            title: 'Welcome!',
            text: 'Bank Appointment System helps you book your appointment easily',
            icon: 'info',
            confirmButtonColor: '#006e3d',
            confirmButtonText: 'Start Now'
        });
        saveToLocalStorage('hasVisited', true);
    }
}

// Export functions for general use
window.bankAppointmentSystem = {
    showNotification,
    confirmDelete,
    startAppointmentCountdown,
    validateAppointmentForm,
    formatDate,
    formatTime,
    searchTable,
    printElement,
    showLoader,
    hideLoader,
    saveToLocalStorage,
    getFromLocalStorage,
    showWelcomeMessage
};
