// logout.js

// Check if username and email exist in localStorage
const username = localStorage.getItem('username');
const email = localStorage.getItem('email');

// Redirect to signin.php if username or email is missing
if (!username || !email) {
    window.location.href = 'signin.php';
}

// Logout functionality
document.addEventListener('DOMContentLoaded', function () {
    const logoutButton = document.getElementById('logoutButton');
    if (logoutButton) {
        logoutButton.addEventListener('click', function (event) {
            event.preventDefault(); // Prevent default anchor behavior

            // Show confirmation alert
            const confirmLogout = confirm('Are you sure you want to log out?');
            if (confirmLogout) {
                // Clear username and email from localStorage
                localStorage.removeItem('username');
                localStorage.removeItem('email');

                // Redirect to signin.php
                window.location.href = 'signin.php';
            }
        });
    }
});