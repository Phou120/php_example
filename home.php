<?php
// Include the database connection
require_once 'db.php';
require_once 'time_helpers.php';
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
    $post_id = $_POST['post_id'];
    $comment = trim($_POST['comment']); // Trim whitespace
    $user_id = $_SESSION['user_id']; // Ensure the user is logged in

    if (!empty($comment)) {
        // Use prepared statements for security
        $stmt = $conn->prepare("INSERT INTO comments (user_id, content, post_id) VALUES (?, ?, ?)");
        $stmt->bind_param("isi", $user_id, $comment, $post_id);

        if ($stmt->execute()) {
            header("Location: " . $_SERVER['REQUEST_URI']); // Refresh page without resubmitting
            exit();
        } else {
            echo "<p class='text-red-600 text-sm mb-4'>Error: " . $conn->error . "</p>";
        }
        $stmt->close();
    } else {
        echo "<p class='text-red-600 text-sm mb-4'>Comment cannot be empty.</p>";
    }
}



$postId = $_GET['post_id'] ?? 0;

$commentsQuery = "SELECT comments.id, comments.content, users.full_name 
        FROM comments 
        INNER JOIN users ON comments.user_id = users.id
        WHERE comments.post_id = ?
        ORDER BY comments.created_at DESC";

$stmt = $conn->prepare($commentsQuery);
$stmt->bind_param("i", $post_id);
$stmt->execute();
$commentsResult = $stmt->get_result();

// Get the total number of comments
$totalComments = $commentsResult->num_rows;



// Post ID for like system
$postId = $_POST['post_id'] ?? 0;

// Handling Like/Unlike system
$isLiked = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_like'])) {
    // Validate if the post exists in the 'posts' table
    $checkPostQuery = "SELECT * FROM posts WHERE id = ?";
    if ($stmt = $conn->prepare($checkPostQuery)) {
        $stmt->bind_param("i", $postId);
        if ($stmt->execute()) {
            $postResult = $stmt->get_result();
            
            // Check if post exists
            if ($postResult->num_rows === 0) {
                echo "<p class='text-red-600 text-sm mb-4'>Error: Post does not exist.</p>";
                exit();
            }
        } else {
            echo "<p class='text-red-600 text-sm mb-4'>Error executing query: " . $stmt->error . "</p>";
            exit();
        }
        $stmt->close();
    }

    // Handle like/unlike toggle action
    if (isset($_POST['toggle_like'])) {
        // Check if the user has already liked the post
        $checkLikeQuery = "SELECT * FROM likes WHERE user_id = ? AND post_id = ?";
        if ($stmt = $conn->prepare($checkLikeQuery)) {
            $stmt->bind_param("ii", $user_id, $postId);
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                
                if ($result->num_rows === 0) {
                    // If user has not liked the post, add a new like
                    $sql = "INSERT INTO likes (user_id, post_id) VALUES (?, ?)";
                    if ($stmt = $conn->prepare($sql)) {
                        $stmt->bind_param("ii", $user_id, $postId);
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
                        $stmt->bind_param("ii", $user_id, $postId);
                        if ($stmt->execute()) {
                            // Redirect to refresh the page after unliking
                            header("Location: #");
                            exit();
                        } else {
                            echo "<p class='text-red-600 text-sm mb-4'>Error: " . $stmt->error . "</p>";
                        }
                    }
                }
            } else {
                echo "<p class='text-red-600 text-sm mb-4'>Error executing query: " . $stmt->error . "</p>";
            }
        }
    }
}

// Check if the current user has liked the post
$userLikedQuery = "SELECT * FROM likes WHERE user_id = ? AND post_id = ?";
if ($stmt = $conn->prepare($userLikedQuery)) {
    $stmt->bind_param("ii", $user_id, $post_id);
    $stmt->execute();
    $likedResult = $stmt->get_result();
    $isLiked = $likedResult->num_rows > 0;
}

    
// post data 
// Handle post submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    // Validate input
    $content = trim($_POST['content']);
    
    // Check if content is not empty
    if (!empty($content)) {
        // Prepare uploads directory
        $uploadDir = __DIR__ . "/uploads/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Initialize image path
        $imagePath = null;

        // Handle file upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $image = $_FILES['image'];
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            
            // Validate file type
            if (in_array($image['type'], $allowedTypes)) {
                // Generate unique filename
                $fileName = uniqid() . '_' . basename($image['name']);
                $imagePath = $uploadDir . $fileName;

                // Move uploaded file
                if (move_uploaded_file($image['tmp_name'], $imagePath)) {
                    // Convert to relative path for database storage
                    $imagePath = 'uploads/' . $fileName;
                } else {
                    // File upload failed
                    $imagePath = null;
                }
            }
        }

        // Begin transaction for atomic operation
        $conn->begin_transaction();

        try {
            // Insert post
            $postQuery = "INSERT INTO posts (user_id, content, created_at) VALUES (?, ?, NOW())";
            $stmt = $conn->prepare($postQuery);
            $stmt->bind_param("is", $user_id, $content);
            $stmt->execute();
            $post_id = $stmt->insert_id;

            // Insert image if exists
            if ($imagePath) {
                $imageQuery = "INSERT INTO images (post_id, image_path) VALUES (?, ?)";
                $imgStmt = $conn->prepare($imageQuery);
                $imgStmt->bind_param("is", $post_id, $imagePath);
                $imgStmt->execute();
            }

            // Commit transaction
            $conn->commit();

            // Redirect to prevent form resubmission
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();

        } catch (Exception $e) {
            // Rollback transaction on error
            $conn->rollback();
            echo "Error: " . $e->getMessage();
        }
    }
}

$query = "
    SELECT 
        p.id AS post_id, 
        p.user_id,
        p.content AS content, 
        p.created_at, 
        u.full_name AS user_name, 
        u.profile_picture,
        i.image_path, 
        c.id AS comment_id, 
        c.content AS comment_content, 
        c.created_at AS comment_created_at, 
        cu.full_name AS comment_user_name,  -- Comma added here
        cu.profile_picture AS comment_profile_picture
    FROM posts p
    LEFT JOIN users u ON p.user_id = u.id
    LEFT JOIN images i ON p.id = i.post_id
    LEFT JOIN comments c ON p.id = c.post_id
    LEFT JOIN users cu ON c.user_id = cu.id
    ORDER BY p.created_at DESC, c.created_at DESC
";

$result = $conn->query($query);
$posts = [];

// Organize posts and comments
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $post_id = $row['post_id'];

        // Initialize the post if it doesn't exist
        if (!isset($posts[$post_id])) {
            $posts[$post_id] = [
                'id' => $row['post_id'],
                'user_id' => $row['user_id'],
                'content' => $row['content'],
                'created_at' => $row['created_at'],
                'user_name' => $row['user_name'],
                'image_path' => $row['image_path'],
                'profile_picture' => $row['profile_picture'],
                'comments' => []
            ];
        }

        // Add the comment to the post if it exists
        if ($row['comment_id']) {
            $posts[$post_id]['comments'][] = [
                'id' => $row['comment_id'],
                'content' => $row['comment_content'],
                'created_at' => $row['comment_created_at'],
                'user_name' => $row['comment_user_name'],
                'comment_profile_picture' => $row['comment_profile_picture']
            ];
        }
    }
}


// Assuming $user_id is the user ID for each comment
$user_query = "SELECT profile_picture FROM users WHERE id = ?";
$user_stmt = $conn->prepare($user_query);
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user_data = $user_result->fetch_assoc();  // Fetch the user data

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Humback</title>
    <link rel="icon" href="img/letter-h-logo-gold-free-png.png" type="image/png">
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans&display=swap" rel="stylesheet">

</head>

<body class="dark:bg-gray-900 flex justify-center p-6 transition duration-300">

    <div class="w-full max-w-2xl bg-white dark:bg-gray-800 shadow-xl rounded-2xl p-5 transition duration-300">
        <!-- Header -->
        <header class="flex items-center justify-between border-b pb-4">
            <h1 class="text-3xl font-extrabold text-blue-600 dark:text-blue-400">Humback</h1>

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
                <?php
                // Assuming $user_id is the user ID for each comment
                // $user_query = "SELECT profile_picture FROM users WHERE id = ?";
                // $user_stmt = $conn->prepare($user_query);
                // $user_stmt->bind_param("i", $user_id);
                // $user_stmt->execute();
                // $user_result = $user_stmt->get_result();
                // $user_data = $user_result->fetch_assoc();  // Fetch the user data

                // Check if user is found, otherwise use default image
                $comment_profile_picture = $user_data ? $user_data['profile_picture'] : 'https://randomuser.me/api/portraits/men/45.jpg';  
                ?>
                <!-- <img src="https://randomuser.me/api/portraits/men/45.jpg" class="w-12 h-12 rounded-full"> -->
                <img src="<?php echo htmlspecialchars($comment_profile_picture); ?>" class="w-10 h-10 rounded-full"
                    alt="Post Image">
                <p><?php echo $name; ?></p>
                <p>|</p>
                <button style="color: red;" onclick="logout()">logout</button>

            </div>
        </header>

        <!-- Create Post -->
        <!-- Main Input Field -->
        <div class="mt-5 flex items-center space-x-3 p-4 bg-white shadow rounded-lg">
            <img src="<?php echo htmlspecialchars($comment_profile_picture); ?>" class="w-10 h-10 rounded-full"
                alt="Post Image">
            <input type="text" id="postInput" placeholder="What's on your mind?" readonly
                class="flex-1 px-4 py-2 border rounded-full bg-gray-100 cursor-pointer focus:outline-none">
        </div>

        <!-- Modal -->
        <div id="postModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden">
            <div class="bg-gray-900 p-5 rounded-lg shadow-lg w-96 text-white">
                <!-- Modal Header -->
                <div class="flex justify-between items-center border-b pb-2">
                    <h2 class="text-lg font-semibold">Create Post</h2>
                    <button id="closeModal" class="text-gray-400 hover:text-gray-200">&times;</button>
                </div>

                <!-- Form -->
                <form method="POST" enctype="multipart/form-data">
                    <div class="mt-4">
                        <textarea name="content"
                            class="w-full h-24 p-2 border rounded-lg bg-gray-800 text-white overflow-y-auto"
                            placeholder="What's on your mind?"></textarea>
                    </div>

                    <!-- Image Preview -->
                    <div id="imagePreview" class="mt-3 hidden overflow-y-auto" style="max-height: 300px;">
                        <img id="previewImg" class="w-full rounded-lg">
                    </div>

                    <!-- Options -->
                    <div class="flex items-center space-x-4 mt-3">
                        <label class="cursor-pointer flex items-center space-x-1">
                            <input type="file" name="image" id="imageUpload" class="hidden">
                            <span class="text-blue-400">📷 Photo</span>
                        </label>

                        <button type="button" id="feelingBtn" class="text-yellow-400">😊 Feeling</button>
                        <button type="button" id="tagBtn" class="text-blue-400">👥 Tag</button>
                        <button type="button" id="checkInBtn" class="text-red-400">📍 Check-in</button>
                        <a id="whatsappShare" href="#" target="_blank" class="text-green-400">📲 WhatsApp</a>
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-4 flex justify-end">
                        <button type="submit" name="submit"
                            class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">
                            Post
                        </button>
                    </div>
                    .
                </form>

            </div>
        </div>

        <!-- Post -->
        <?php foreach ($posts as $post): ?>
        <div class="mt-6 bg-white dark:bg-gray-800 p-5 rounded-2xl shadow-lg transition duration-300">
            <!-- Post Header (User Info) -->
            <div class="flex items-center justify-between p-2">
                <!-- Left Section: Profile Image & User Info -->
                <div class="flex items-center space-x-3">
                    <img src="<?php echo htmlspecialchars($post['profile_picture']); ?>" class="w-10 h-10 rounded-full"
                        alt="Post Image">
                    <div>
                        <p class="font-semibold dark:text-white" style="font-family: 'Noto Sans', sans-serif;">
                            <?php echo htmlspecialchars($post['user_name']); ?></p>
                        <p class="text-sm text-gray-500"><?php echo timeAgo($post['created_at']) . ' ago'; ?></p>
                    </div>
                </div>

                <!-- Right Section: Dots Menu (Only for Post Owner) -->
                <?php if ($post['user_id'] == $_SESSION['user_id']) : ?>
                <div class="relative ml-auto">
                    <button onclick="toggleMenu(this)" class="text-gray-500 hover:text-gray-800 focus:outline-none">
                        <i class="fas fa-ellipsis-h text-lg"></i> <!-- Horizontal Dots -->
                    </button>

                    <!-- Dropdown Menu -->
                    <div class="absolute right-0 mt-2 w-32 bg-white border rounded-md shadow-lg hidden menu-dropdown">
                        <!-- Edit button with edit icon -->
                        <button
                            onclick="openEditModal(<?php echo $post['id']; ?>, '<?php echo htmlspecialchars($post['content']); ?>', '<?php echo $post['image_path']; ?>')"
                            class="block px-4 py-2 text-gray-700 hover:bg-gray-200 w-full text-left">
                            <i class="fas fa-edit text-blue-600 mr-2"></i> <!-- Edit Icon -->
                            Edit
                        </button>
                        <!-- Delete button with delete icon -->
                        <a href="delete_post.php?id=<?php echo $post['id']; ?>"
                            class="block px-4 py-2 text-gray-700 hover:bg-gray-200 flex items-center justify-start">
                            <i class="fas fa-trash text-red-600 mr-2"></i> <!-- Trash Icon -->
                            Delete
                        </a>
                    </div>

                </div>
                <?php endif; ?>

            </div>

            <!-- Edit Post Modal -->
            <div id="editModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden flex items-center justify-center">
                <div class="bg-white p-5 rounded-lg shadow-lg w-96">
                    <h2 class="text-lg font-semibold mb-4">Edit Post</h2>
                    <form id="editPostForm" action="update_post.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="post_id" id="editPostId">

                        <!-- Post Content -->
                        <label class="block text-sm font-medium text-gray-700">Content</label>
                        <textarea name="content" id="editPostContent" class="w-full border p-2 rounded-md"></textarea>

                        <!-- Current Image Preview -->
                        <div class="mt-2">
                            <img id="editPostImagePreview" src="" class="w-full h-40 object-cover rounded-md">
                        </div>

                        <!-- Upload New Image -->
                        <label class="block mt-2 text-sm font-medium text-gray-700">Upload New Image</label>
                        <input type="file" name="image" class="w-full border p-2 rounded-md">

                        <!-- Buttons -->
                        <div class="flex justify-end space-x-2 mt-4">
                            <button type="button" onclick="closeEditModal()"
                                class="px-4 py-2 bg-gray-300 rounded-md">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md">Save
                                Changes</button>
                        </div>
                    </form>
                </div>
            </div>


            <!-- Post Content -->
            <div class="mt-3 text-gray-700 dark:text-gray-300" style="font-family: 'Noto Sans', sans-serif;">
                <?php echo nl2br(htmlspecialchars($post['content'])); ?>
            </div>


            <!-- Post Image -->
            <div class="mt-3 rounded-xl overflow-hidden shadow-md">
                <?php if (!empty($post['image_path'])): ?>
                <img src="<?php echo htmlspecialchars($post['image_path']); ?>" class="w-full" alt="Post Image">
                <?php else: ?>
                <p class="text-gray-500 italic">No image available</p>
                <?php endif; ?>
            </div>

            <!-- Post Actions (Like, Comment, Share) -->
            <div class="mt-4 flex justify-between text-sm text-gray-500 border-t pt-3">
                <!-- Like Button -->
                <form method="POST" action="">
                    <input type="hidden" name="post_id" value="<?php 
                    var_dump($post['id']);
                    echo htmlspecialchars($post['id']); 
                    ?>">
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

                <!-- Comment Button -->
                <button class="hover:text-blue-500 transition">
                    <i class="fas fa-comment"></i> Comments (<?php echo $totalComments; ?>)
                </button>

                <!-- Share Button -->
                <button class="hover:text-blue-500 transition">
                    <i class="fas fa-share"></i> Share (2)
                </button>
            </div>



            <!-- Comment Section -->
            <div class="mt-4 border-t pt-3">
                <!-- Comment Input Box -->
                <form method="POST" action="">
                    <div class="flex items-center space-x-2 mb-3">
                        <img src="<?php echo htmlspecialchars($comment_profile_picture); ?>"
                            class="w-10 h-10 rounded-full" alt="Post Image">
                        <input type="hidden" name="post_id" value="<?php echo htmlspecialchars($post['id']); ?>">
                        <input type="text" name="comment" placeholder="Write a comment..."
                            class="flex-1 px-4 py-2 border rounded-full dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-400"
                            required>
                        <button type="submit" name="submit"
                            class="px-4 py-2 bg-blue-500 text-white rounded-xl hover:bg-blue-600 transition">Post</button>
                    </div>
                </form>

                <!-- Comments List -->
                <div class="space-y-3">
                    <?php
                        $comments = $post['comments'];
                        $totalComments = count($comments);

                        // Display the first 2 comments
                        for ($i = 0; $i < min(2, $totalComments); $i++) {
                            $comment = $comments[$i];
                            
                            $comment_profile_picture = $comments[$i]['comment_profile_picture'];  // Default image if not available

                            echo "<div class='flex items-start space-x-2'>
                                    <img src='" . htmlspecialchars($comment_profile_picture) . "' class='w-8 h-8 rounded-full' alt='Post Image'>
                                    <div>
                                        <p class='font-semibold dark:text-white'>{$comment['user_name']}</p>
                                        <p class='text-sm text-gray-500'>{$comment['content']}</p>
                                    </div>
                                </div>";
                        }

                        // Display "View More" link if there are more than 2 comments
                        if ($totalComments > 2) {
                            echo "<div id='more-comments-{$post['id']}' class='hidden'>";
                            for ($i = 2; $i < $totalComments; $i++) {
                                $comment = $comments[$i];
                                $comment_profile_picture = $comments[$i]['comment_profile_picture'];  // Default image if not available

                                echo "<div class='flex items-start space-x-2 mt-2'>
                                        <img src='" . htmlspecialchars($comment_profile_picture ?? 'https://randomuser.me/api/portraits/men/45.jpg') . "' class='w-8 h-8 rounded-full' alt='Post Image'>
                                        <div>
                                            <p class='font-semibold dark:text-white'>{$comment['user_name']}</p>
                                            <p class='text-sm text-gray-500'>{$comment['content']}</p>
                                        </div>
                                    </div>";
                            }
                            echo "</div>";
                            echo "<a href='#' onclick='toggleComments({$post['id']})' class='text-blue-500 text-sm mt-2'>View More</a>";
                        }
                    ?>
                </div>

            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <script src="js/home.js"></script>
</body>

</html>

<?php
// Close the database connection
$conn->close();
?>