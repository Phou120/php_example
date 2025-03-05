<?php
// Include database connection
require_once 'db.php';

// Initialize error messages and success message
$errors = ['full_name' => '', 'email' => '', 'password' => ''];
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data and trim spaces
    $name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validate full name
    if (empty($name)) {
        $errors['full_name'] = 'Full Name is required.';
    }

    // Validate email
    if (empty($email)) {
        $errors['email'] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format.';
    } else {
        // Check if email already exists
        $check_email = "SELECT id FROM users WHERE email = ?";
        $stmt = $conn->prepare($check_email);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $errors['email'] = 'Email already exists. Please use another email.';
        }
        $stmt->close();
    }

    // Validate password
    if (empty($password)) {
        $errors['password'] = 'Password is required.';
    } elseif (strlen($password) < 6) {
        $errors['password'] = 'Password must be at least 6 characters long.';
    }

    // If no errors, proceed with registration
    if (!array_filter($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $name, $email, $hashed_password);

        if ($stmt->execute()) {
            $success_message = "<p class='text-green-600 text-sm mb-4'>User registered successfully!</p>";
        } else {
            echo "<p class='text-red-600 text-sm mb-4'>Error: " . $conn->error . "</p>";
        }
        $stmt->close();
    }

    // Close the connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 md:flex md:items-center md:justify-center min-h-screen">
    <div class="md:bg-white p-6 rounded-lg md:shadow-md md:max-w-md w-full">
        <h1 class="text-2xl font-bold mb-2">Create Account</h1>

        <!-- Display success message -->
        <?= $success_message ?>

        <form method="POST" action="">
            <!-- Full Name Input -->
            <div class="mb-4">
                <label for="full_name" class="block text-left text-sm font-medium text-gray-700">Full Name</label>
                <input type="text" id="full_name" name="full_name" value="<?= htmlspecialchars($name ?? '') ?>"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                <p class="text-red-500 text-sm"><?= $errors['full_name'] ?></p>
            </div>

            <!-- Email Input -->
            <div class="mb-4">
                <label for="email" class="block text-left text-sm font-medium text-gray-700">Email</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                <p class="text-red-500 text-sm"><?= $errors['email'] ?></p>
            </div>

            <!-- Password Input -->
            <div class="mb-4">
                <label for="password" class="block text-left text-sm font-medium text-gray-700">Password</label>
                <input type="password" id="password" name="password"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                <p class="text-red-500 text-sm"><?= $errors['password'] ?></p>
            </div>

            <!-- Register Button -->
            <button type="submit"
                class="w-full bg-blue-600 text-white py-2 rounded-md mb-4 font-semibold">Register</button>

            <!-- Sign In Option -->
            <p class="text-center text-gray-500 text-sm">Have an account? <a href="signin.php"
                    class="text-green-600 font-semibold">Sign In</a></p>
        </form>
    </div>
</body>

</html>