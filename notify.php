<?php
session_start();
include('db.php');

// Check if the user is logged in  
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get the user ID from the session  
$user_id = $_SESSION['user_id'];

// Fetch notifications for the logged-in user  
$sql = "SELECT * FROM notifications   
        WHERE user_id = ?   
        ORDER BY timestamp DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id); // Assuming user_id is an integer  
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <div class="container mt-5">
        <h1>Notifications</h1>

        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $notification_id = $row['notification_id'];
                $message = htmlspecialchars($row['message']); // Prevent XSS  
                $timestamp = date("Y-m-d H:i:s", strtotime($row['timestamp'])); // Format timestamp  
                $read_status = $row['read_status'];

                // Mark as read when clicked (this logic should be handled separately, e.g., via a button)  
                if ($read_status == 'unread') {
                    // Create a link or button to mark as read  
                    echo "  
                    <div class='card mb-3'>  
                        <div class='card-body'>  
                            <p><strong>$message</strong></p>  
                            <p class='text-muted'>$timestamp</p>  
                            <form method='post' action='mark_as_read.php'>  
                                <input type='hidden' name='notification_id' value='$notification_id'>  
                                <button type='submit' class='btn btn-link'>Mark as Read</button>  
                            </form>  
                        </div>  
                    </div>";
                } else {
                    echo "  
                    <div class='card mb-3'>  
                        <div class='card-body'>  
                            <p><strong>$message</strong></p>  
                            <p class='text-muted'>$timestamp</p>  
                        </div>  
                    </div>";
                }
            }
        } else {
            echo "<p>No notifications.</p>";
        }
        ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>