<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Kupa</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 flex md:items-center md:justify-center h-dvh">
    <div class="md:bg-white p-6 rounded-lg md:shadow-md md:max-w-md w-full md:flex md:flex-col">
        <p class="text-gray-500 mb-6">Login</p>
        <!-- Form -->
        <form method="POST" action="">
            <?php
            // Include the global configuration file
            require_once 'db.php';

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Get form data and sanitize inputs
                $email = $conn->real_escape_string($_POST['email']);
                $password = $_POST['password'];

                // Query the database for the user
                $sql = "SELECT id, full_name, email, password FROM users WHERE email='$email'";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    $user = $result->fetch_assoc();
                    if (password_verify($password, $user['password'])) {
                        // Start a session and store user data
                        session_start();
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['full_name'] = $user['full_name'];
                        $_SESSION['email'] = $user['email'];

                        // Log user data to a file
                        $ip = $_SERVER['REMOTE_ADDR']; // Get the user's IP address
                        $logMessage = "User ID: {$user['id']}, full_name: {$user['full_name']}, Email: {$user['email']}, IP: $ip, Login Time: " . date('Y-m-d H:i:s') . PHP_EOL;
                        file_put_contents('logLogin.log', $logMessage, FILE_APPEND);

                       // Pass user data to JavaScript for localStorage
                        echo "<script>
                        localStorage.setItem('id', '" . addslashes($user['id']) . "');
                        localStorage.setItem('full_name', '" . addslashes($user['full_name']) . "');
                        localStorage.setItem('email', '" . addslashes($user['email']) . "');
                        window.location.href = 'home.php';
                        </script>";
                        exit(); // Ensure no further code is executed after redirection
                    } else {
                        echo "<p class='text-red-600 text-sm mb-4'>Invalid email or password.</p>";
                    }
                } else {
                    echo "<p class='text-red-600 text-sm mb-4'>Invalid email or password.</p>";
                }

                // Close the connection
                $conn->close();
            }
            ?>
            <!-- Email Input -->
            <div class="mb-4">
                <label for="email" class="block text-left text-sm font-medium text-gray-700">Email</label>
                <input type="email" id="email" name="email" placeholder="Your email"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm"
                    required>
            </div>
            <!-- Password Input -->
            <div class="mb-4">
                <label for="password" class="block text-left text-sm font-medium text-gray-700">Password</label>
                <div class="relative">
                    <input type="password" id="password" name="password" placeholder="Your password"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm"
                        required>
                    <button type="button" class="absolute inset-y-0 right-3 flex items-center text-gray-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" width="32" height="32"
                            viewBox="0 0 24 24">
                            <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="1.5">
                                <path
                                    d="M2.036 12.322a1 1 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178c.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178" />
                                <path d="M15 12a3 3 0 1 1-6 0a3 3 0 0 1 6 0" />
                            </g>
                        </svg>
                    </button>
                </div>
            </div>
            <!-- Login Button -->
            <button type="submit"
                class="w-full bg-blue-600 text-white py-2 rounded-md mb-4 font-semibold">Login</button>
            <!-- Sign Up Option -->
            <p class="text-center text-gray-500 text-sm">Don't have an account? <a href="signup.php"
                    class="text-green-600 font-semibold">Create Account</a></p>
        </form>
    </div>

</body>

</html>