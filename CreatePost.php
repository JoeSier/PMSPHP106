<?php
global $mysqli;
include('partial/header.php');
include('sidebar.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = $mysqli->real_escape_string(trim($_POST['post_title']));
    $content = $mysqli->real_escape_string(trim($_POST['post_content']));
    $post_by = $_SESSION['UserID'] ?? 0; // Assuming session contains user ID
    $post_date = date("Y-m-d H:i:s");

    // Prepare and execute the SQL statement
    $stmt = $mysqli->prepare("INSERT INTO posts (Title, post_content, post_date, post_by) VALUES (?,?,?,?)");

    if ($stmt) {
        $stmt->bind_param("sssi", $title, $content, $post_date, $post_by);

        if ($stmt->execute()) {
            echo "Post created successfully!";
        } else {
            echo "Error creating post: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Failed to prepare the SQL statement.";
    }

    $mysqli->close();
}

?>
<body>
<div class="dashContent">
    <h1>Create a New Post</h1>
    <form method="post" action="">
        <div>
            <label for="post_title">Title:</label>
            <input type="text" id="post_title" name="post_title" required minlength="3" maxlength="100">
        </div>

        <div>
            <label for="post_content">Content:</label>
            <textarea id="post_content" name="post_content" rows="6" required minlength="10"></textarea>
        </div>

        <div>
            <button type="submit">Create Post</button>
        </div>
    </form>

</div>
</body>