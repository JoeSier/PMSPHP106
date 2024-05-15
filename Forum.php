<?php
include('partial/header.php');
include('sidebar.php');

$stmt = $mysqli->prepare(
    "SELECT p.post_id, p.Title, p.post_content, p.post_date, a.Username AS poster_username 
     FROM posts p
     JOIN account a ON p.post_by = a.UserID
     ORDER BY p.post_date DESC"
);

if ($stmt) {
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<div class='dashContent'>";
        echo "<h1>Forum Conversations</h1>";
        echo "<a href='/CreatePost.php'>Make a new conversation</a>";

        // Display each post with the poster's username
        while ($row = $result->fetch_assoc()) {
            echo "<div class='forum-post'>";
            echo "<h2><a href='/view_post.php?post_id=" . $row['post_id'] . "'>" . htmlspecialchars($row['Title']) . "</a></h2>";
            echo "<p>Posted on " . date('F j, Y, g:i a', strtotime($row['post_date'])) . " by " . htmlspecialchars($row['poster_username']) . "</p>";
            echo "<p>" . nl2br(htmlspecialchars($row['post_content'])) . "</p>";
            echo "</div>";
        }

        echo "</div>";
    } else {
        echo "<div class='box_dash_other'>";
        echo "<h1 class='h1' >Forum Conversations</h1>";
        echo "<a class='button' id='forum' href='/CreatePost.php'>Make a new conversation</a>";
        echo "<p class='text'>No conversations found. Start a new one!</p>";
        echo "</div>";
    }

    $result->free();
    $stmt->close();
} else {
    echo "Failed to prepare the SQL statement.";
}

$mysqli->close();
?>
