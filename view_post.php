<?php
include('partial/header.php');
include('sidebar.php');

$post_id = isset($_GET['post_id']) ? intval($_GET['post_id']) : 0;

$post_stmt = $mysqli->prepare(
    "SELECT p.*, a.Username AS poster_username 
     FROM posts p
     JOIN account a ON p.post_by = a.UserID
     WHERE p.post_id = ?"
);
$post_stmt->bind_param("i", $post_id);

if ($post_stmt->execute()) {
    $post_result = $post_stmt->get_result();
    if ($post_result->num_rows === 1) {
        $post = $post_result->fetch_assoc();
        echo "<div class='dashContent'>";
        echo "<h1>" . htmlspecialchars($post['Title']) . "</h1>";
        echo "<p>Posted on " . date('F j, Y, g:i a', strtotime($post['post_date'])) . " by " . htmlspecialchars($post['poster_username']) . "</p>";
        echo "<div>" . nl2br(htmlspecialchars($post['post_content'])) . "</div>";

        $reply_stmt = $mysqli->prepare(
            "SELECT r.*, a.Username AS replier_username 
             FROM replies r
             JOIN account a ON r.reply_by = a.UserID
             WHERE r.post_id = ? 
             ORDER BY r.reply_date ASC"
        );
        $reply_stmt->bind_param("i", $post_id);

        if ($reply_stmt->execute()) {
            $reply_result = $reply_stmt->get_result();

            echo "<div class='forum-post'>";
            echo "<h2>Replies</h2>";
            echo "</div>";

            while ($reply = $reply_result->fetch_assoc()) {
                echo "<div class='reply'>";
                echo "<p>Reply by " . htmlspecialchars($reply['replier_username']) . " on " . date('F j, Y, g:i a', strtotime($reply['reply_date'])) . "</p>";
                echo "<p>" . nl2br(htmlspecialchars($reply['reply_content'])) . "</p>";
                echo "</div>";
            }

            $reply_result->free();
        }

        echo "<h3>Add a Reply</h3>";
        echo "<form method='post'>";
        echo "<textarea name='reply_content' rows='4' required></textarea>";
        echo "<button type='submit'>Submit Reply</button>";
        echo "</form>";
        echo "</div>";
    }

    $post_stmt->close();
} else {
    echo "Error fetching the post details.";
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $reply_content = $mysqli->real_escape_string(trim($_POST['reply_content']));
    $reply_by = $_SESSION['UserID'];
    $reply_date = date("Y-m-d H:i:s");

    $add_reply_stmt = $mysqli->prepare(
        "INSERT INTO replies (post_id, reply_content, reply_date, reply_by) 
        VALUES (?, ?, ?, ?)"
    );
    $add_reply_stmt->bind_param("isss", $post_id, $reply_content, $reply_date, $reply_by);

    if ($add_reply_stmt->execute()) {
        // Redirect to avoid duplicate form submissions
        header("Location: view_post.php?post_id=" . $post_id);
        exit;
    } else {
        echo "Error adding reply: " . $add_reply_stmt->error;
    }

    $add_reply_stmt->close();
}

$mysqli->close();
?>
