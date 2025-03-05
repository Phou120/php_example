// logout.js

// Check if username and email exist in localStorage
const full_name = localStorage.getItem('full_name');
const email = localStorage.getItem('email');
const id = localStorage.getItem('id');
console.log(full_name + ' ' + email + ' ' + id);

// // Redirect to signin.php if username or email is missing
if (!full_name || !email || !id) {
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
                localStorage.removeItem('id');
                localStorage.removeItem('full_name');
                localStorage.removeItem('email');

                // Redirect to signin.php
                window.location.href = 'signin.php';
            }
        });
    }
});