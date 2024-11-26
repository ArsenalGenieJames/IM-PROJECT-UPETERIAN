<?php
session_start();
include('db.php');

// Check if user is logged in  
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch posts with like count and profile picture  
$query = "SELECT Posts.*, Users.username, Users.profile_picture,  
                 (SELECT COUNT(*) FROM Likes WHERE post_id = Posts.post_id) as like_count   
          FROM Posts   
          JOIN Users ON Posts.user_id = Users.user_id   
          ORDER BY timestamp DESC";

$result = $conn->query($query);
if (!$result) {
    die("Database query failed: " . $conn->error);
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="tabfooter.css">
</head>

<body style="background-color: white;">
    <header class="py-1">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-auto">
                    <img src="logo.png" alt="secondary logo" style="height: 60px; width: 85px;">
                </div>
            </div>
        </div>
    </header>

    <div id="newsfeed">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<div class="card mb-3">';
                echo '<div class="card-body">';
                // Display profile picture
                $profilePic = !empty($row['profile_picture']) ? htmlspecialchars($row['profile_picture']) : 'default-pic.jpg';
                echo '<div class="d-flex align-items-center">';
                echo '<img src="' . $profilePic . '" alt="Profile Picture" class="rounded-circle me-3" style="width: 40px; height: 40px;">';
                echo '<h5 class="card-title">' . htmlspecialchars($row['username']) . '</h5>';
                echo '</div>';
                echo '<p class="card-text mt-2">' . htmlspecialchars($row['content']) . '</p>';

                // Check if there's a media URL (image or video)
                if (!empty($row['media_url'])) {
                    $media_url = htmlspecialchars($row['media_url']);
                    $file_extension = pathinfo($media_url, PATHINFO_EXTENSION);

                    if (in_array($file_extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                        echo '<img src="' . $media_url . '" alt="Post Image" class="img-fluid">';
                    } elseif (in_array($file_extension, ['mp4', 'avi', 'mov'])) {
                        echo '<video controls class="img-fluid">';
                        echo '<source src="' . $media_url . '" type="video/' . $file_extension . '">';
                        echo 'Your browser does not support the video tag.';
                        echo '</video>';
                    } else {
                        echo '<p>Unsupported media type.</p>';
                    }
                }

                echo '<p class="card-text mt-2"><small class="text-muted">' . htmlspecialchars($row['timestamp']) . '</small></p>';

                // Display like button
                echo '<form action="like.php" method="post" class="d-inline">';
                echo '<input type="hidden" name="post_id" value="' . htmlspecialchars($row['post_id']) . '">';
                echo '<button type="submit" class="btn btn-sm btn-link">Like (' . htmlspecialchars($row['like_count']) . ')</button>';
                echo '</form>';

                // Show Comments button
                echo '<button class="btn btn-sm btn-link show-comments" data-post-id="' . htmlspecialchars($row['post_id']) . '">Show Comments</button>';

                // Display comments 
                echo '<div class="comments" id="comments-' . htmlspecialchars($row['post_id']) . '" style="display: none;">';
                echo '<h6>Comments:</h6>';
                $comments_query = "SELECT Comments.*, Users.username FROM Comments
                                   JOIN Users ON Comments.user_id = Users.user_id
                                   WHERE post_id = ?";
                $stmt_comments = $conn->prepare($comments_query);
                $stmt_comments->bind_param("i", $row['post_id']);
                $stmt_comments->execute();
                $comments_result = $stmt_comments->get_result();

                if ($comments_result->num_rows > 0) {
                    while ($comment = $comments_result->fetch_assoc()) {
                        echo '<div><strong>' . htmlspecialchars($comment['username']) . ':</strong> ' . htmlspecialchars($comment['content']) . '</div>';
                    }
                } else {
                    echo '<div>No comments yet.</div>';
                }

                // Comment form
                echo '<form action="submitcommit.php" method="post">';
                echo '<input type="hidden" name="post_id" value="' . htmlspecialchars($row['post_id']) . '">';
                echo '<input type="hidden" name="user_id" value="' . htmlspecialchars($_SESSION['user_id']) . '">';
                echo '<div><input type="text" name="content" placeholder="Add a comment..." required class="form-control my-2">';
                echo '<button type="submit" class="btn btn-primary btn-sm">Submit</button></div>';
                echo '</form>';

                echo '</div>'; // Close comments div
                echo '</div>'; // Close card-body
                echo '</div>'; // Close card
            }
        } else {
            echo '<p>No posts yet!</p>';
        }
        ?>
    </div>

    <footer class="bg-light">
        <div class="container">
            <div class="row text-center mt-4 footertab">
                <div class="col">
                    <a href="home.php" class="text-decoration-none">
                        <i class="fas fa-home fa-2x footericon" data-page="home.php"></i>
                    </a>
                </div>
                <div class="col">
                    <a href="search.php" class="text-decoration-none">
                        <i class="fas fa-search fa-2x footericon" data-page="search.php"></i>
                    </a>
                </div>
                <div class="col">
                    <a href="post.php" class="text-decoration-none">
                        <i class="fas fa-plus fa-2x footericon" data-page="post.php"></i>
                    </a>
                </div>
                <div class="col">
                    <a href="notify.php" class="text-decoration-none">
                        <i class="fas fa-bell fa-2x footericon" data-page="notify.php"></i>
                    </a>
                </div>
                <div class="col">
                    <a href="profile.php" class="text-decoration-none">
                        <i class="fas fa-user fa-2x footericon" data-page="profile.php"></i>
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <script src="./jscode/jscode.js"></script>
    <script src="./jscode/comment.js"></script>
    
</body>

</html>