<?php
// Include the database connection
require_once 'db.php';
session_start();

// Check if the user is logged in and get user_id from session
if (!isset($_SESSION['user_id'])) {
    // Redirect to login if not logged in
    header("Location: signin.php");
    exit();
}

$user_id = $_SESSION['user_id']; // Get the user_id from session
$name = $_SESSION['full_name'];

// Handling the comment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    // Get the comment data from the form
    $comment = $conn->real_escape_string($_POST['comment']);
    
    // Insert the comment into the database
    $sql = "INSERT INTO comments (user_id, content) VALUES ('$user_id', '$comment')";

    if ($conn->query($sql) === TRUE) {
        // Redirect to refresh the page after successfully posting the comment
        header("Location: #");
        exit();
    } else {
        echo "<p class='text-red-600 text-sm mb-4'>Error: " . $conn->error . "</p>";
    }
}

// Fetch and display comments
// $commentsQuery = "SELECT * FROM comments ORDER BY created_at DESC";
// $commentsResult = $conn->query($commentsQuery);

$commentsQuery = "SELECT comments.id, comments.content, users.full_name 
        FROM comments 
        INNER JOIN users ON comments.user_id = users.id 
        ORDER BY comments.created_at DESC";

$commentsResult = $conn->query($commentsQuery);

// Get the total number of comments
$totalComments = $commentsResult->num_rows;

// Post ID for like system
$post_id = 1;

// Handling Like/Unlike system
$isLiked = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_like'])) {
    // Validate if the post exists in the 'posts' table
    $checkPostQuery = "SELECT * FROM posts WHERE id = ?";
    if ($stmt = $conn->prepare($checkPostQuery)) {
        $stmt->bind_param("i", $post_id);
        $stmt->execute();
        $postResult = $stmt->get_result();
        
        if ($postResult->num_rows === 0) {
            echo "<p class='text-red-600 text-sm mb-4'>Error: Post does not exist.</p>";
            exit();
        }
        $stmt->close();
    }

    // Handle like/unlike toggle action
    if (isset($_POST['toggle_like'])) {
        // Check if the user has already liked the post
        $checkLikeQuery = "SELECT * FROM likes WHERE user_id = ? AND post_id = ?";
        if ($stmt = $conn->prepare($checkLikeQuery)) {
            $stmt->bind_param("ii", $user_id, $post_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 0) {
                // If user has not liked the post, add a new like
                $sql = "INSERT INTO likes (user_id, post_id) VALUES (?, ?)";
                if ($stmt = $conn->prepare($sql)) {
                    $stmt->bind_param("ii", $user_id, $post_id);
                    if ($stmt->execute()) {
                        // Redirect to refresh the page after liking
                        header("Location: #");
                        exit();
                    } else {
                        echo "<p class='text-red-600 text-sm mb-4'>Error: " . $stmt->error . "</p>";
                    }
                }
            } else {
                // If user has already liked the post, remove the like
                $deleteLikeQuery = "DELETE FROM likes WHERE user_id = ? AND post_id = ?";
                if ($stmt = $conn->prepare($deleteLikeQuery)) {
                    $stmt->bind_param("ii", $user_id, $post_id);
                    if ($stmt->execute()) {
                        // Redirect to refresh the page after unliking
                        header("Location: #");
                        exit();
                    } else {
                        echo "<p class='text-red-600 text-sm mb-4'>Error: " . $stmt->error . "</p>";
                    }
                }
            }
        }
    }
}

// Fetch the number of likes for the post
$likeCountQuery = "SELECT COUNT(*) AS like_count FROM likes WHERE post_id = ?";
if ($stmt = $conn->prepare($likeCountQuery)) {
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $likeCount = $result->fetch_assoc()['like_count'];
}

// Check if the current user has liked the post
$userLikedQuery = "SELECT * FROM likes WHERE user_id = ? AND post_id = ?";
if ($stmt = $conn->prepare($userLikedQuery)) {
    $stmt->bind_param("ii", $user_id, $post_id);
    $stmt->execute();
    $likedResult = $stmt->get_result();
    $isLiked = $likedResult->num_rows > 0;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facebook UI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body class="bg-gray-100 dark:bg-gray-900 flex justify-center p-6 transition duration-300">

    <div class="w-full max-w-2xl bg-white dark:bg-gray-800 shadow-xl rounded-2xl p-5 transition duration-300">
        <!-- Header -->
        <header class="flex items-center justify-between border-b pb-4">
            <h1 class="text-3xl font-extrabold text-blue-600 dark:text-blue-400">Facebook</h1>

            <div class="flex-1 flex justify-center mx-4">
                <div class="relative w-full max-w-md">
                    <input type="text" placeholder="Search Facebook"
                        class="w-full px-4 py-2 border rounded-full dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <button
                        class="absolute right-3 top-1/2 transform -translate-y-1/2 p-2 text-gray-500 dark:text-gray-300 hover:text-gray-700 dark:hover:text-gray-100 transition">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>

            <div class="flex items-center space-x-3">
                <button class="text-2xl cursor-pointer dark:text-white" onclick="logout()">
                    <i class="fas fa-user"></i>
                </button>
            </div>
        </header>

        <!-- Create Post -->
        <div class="mt-5 flex items-center space-x-3">
            <img src="https://randomuser.me/api/portraits/men/45.jpg" class="w-12 h-12 rounded-full">
            <input type="text" placeholder="What's on your mind?"
                class="flex-1 px-4 py-2 border rounded-full dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>

        <!-- Post -->
        <div class="mt-6 bg-white dark:bg-gray-800 p-5 rounded-2xl shadow-lg transition duration-300">
            <div class="flex items-center space-x-3">
                <img src="https://randomuser.me/api/portraits/men/45.jpg" class="w-10 h-10 rounded-full">
                <div>
                    <p class="font-semibold dark:text-white"><?php echo $name; ?></p>
                    <p class="text-sm text-gray-500">Johw Jonathan | Flickr - 1h ago</p>
                </div>
            </div>

            <div class="mt-3 rounded-xl overflow-hidden shadow-md">
                <img src="img/8881408094_836058fe2f.jpg" class="w-full">
            </div>

            <div class="mt-4 flex justify-between text-sm text-gray-500 border-t pt-3">
                <!-- Post Like Button -->
                <form method="POST" action="">
                    <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
                    <?php if ($isLiked): ?>
                    <button type="submit" name="toggle_like"
                        class="flex items-center space-x-1 text-blue-500 hover:text-blue-700">
                        <i class="fas fa-thumbs-up"></i> <span>Liked</span>
                    </button>
                    <?php else: ?>
                    <button type="submit" name="toggle_like"
                        class="flex items-center space-x-1 text-gray-500 hover:text-blue-500">
                        <i class="fas fa-thumbs-up"></i> <span>Like</span>
                    </button>
                    <?php endif; ?>
                </form>
                <button class="hover:text-blue-500 transition">
                    <i class="fas fa-comment"></i> Comments (<?php echo $totalComments; ?>)
                </button>
                <button class="hover:text-blue-500 transition">
                    <i class="fas fa-share"></i> Share (2)
                </button>
            </div>

            <!-- Comment Section -->
            <div class="mt-4 border-t pt-3">
                <!-- Comment Input Box -->
                <form method="POST" action="">
                    <div class="flex items-center space-x-2 mb-3">
                        <img src="https://randomuser.me/api/portraits/men/45.jpg" class="w-10 h-10 rounded-full">
                        <input type="text" name="comment" placeholder="Write a comment..."
                            class="flex-1 px-4 py-2 border rounded-full dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-400"
                            required>
                        <button type="submit"
                            class="px-4 py-2 bg-blue-500 text-white rounded-xl hover:bg-blue-600 transition">Post</button>
                    </div>
                </form>

                <!-- Comments List -->
                <div class="space-y-3" id="commentsList">
                    <?php
                    $counter = 0;
                    $commentsArray = [];
                    if ($commentsResult->num_rows > 0) {
                        while ($comment = $commentsResult->fetch_assoc()) {
                            $commentsArray[] = $comment; // Store all comments in an array
                        }

                        // Show first 2 comments
                        for ($i = 0; $i < 2; $i++) {
                            if (isset($commentsArray[$i])) {
                                $comment_content = $commentsArray[$i]['content'];
                                $comment_id = $commentsArray[$i]['id'];

                                // Check if the full name exists in the session
                                $user_name = isset($commentsArray[$i]['full_name']) ? $commentsArray[$i]['full_name'] : 'Anonymous';

                                echo "<div class='flex items-start space-x-2'>
                                        <img src='https://randomuser.me/api/portraits/men/45.jpg' class='w-10 h-10 rounded-full'>
                                        <div>
                                            <p class='font-semibold dark:text-white'>{$user_name}</p> <!-- Show the user's full name -->
                                            <p class='text-sm text-gray-500'>{$comment_content}</p>
                                        </div>
                                    </div>";
                            }
                        }

                        // Add "View More" link if there are more than 2 comments
                        if (count($commentsArray) > 2) {
                            echo "<div id='moreComments' class='hidden'>
                                    <p class='text-sm text-gray-500'>";

                            // Show the rest of the comments
                            for ($i = 2; $i < count($commentsArray); $i++) {
                                $comment_content = $commentsArray[$i]['content'];
                                $comment_id = $commentsArray[$i]['id'];

                                // Use the full name from the session
                                $user_name = isset($commentsArray[$i]['full_name']) ? $commentsArray[$i]['full_name'] : 'Anonymous';

                                echo "<div class='flex items-start space-x-2 mt-2'>
                                        <img src='https://randomuser.me/api/portraits/men/45.jpg' class='w-10 h-10 rounded-full'>
                                        <div>
                                            <p class='font-semibold dark:text-white'>{$user_name}</p> <!-- Show the user's full name -->
                                            <p class='text-sm text-gray-500'>{$comment_content}</p>
                                        </div>
                                    </div>";
                            }
                            echo "</p></div>";

                            // Show the "View More" link
                            echo "<a href='#' onclick='toggleComments()' class='text-blue-500 text-sm mt-2'>View More</a>";
                        }
                    } else {
                        echo "<p>No comments yet.</p>";
                    }
                    ?>
                </div>
            </div>
        </div>

    </div>

    <script>
    // Toggle function to show and hide the additional comments
    function toggleComments() {
        var moreComments = document.getElementById('moreComments');
        var viewMoreLink = event.target;

        if (moreComments.style.display === "none" || moreComments.style.display === "") {
            moreComments.style.display = "block";
            viewMoreLink.innerHTML = "View Less"; // Change text to "View Less"
        } else {
            moreComments.style.display = "none";
            viewMoreLink.innerHTML = "View More"; // Change text to "View More"
        }
    }

    function logout() {
        window.location.href = "signin.php"; // Redirect to signin.php
    }
    </script>
</body>

</html>

<?php
// Close the database connection
$conn->close();
?>