<?php
// Database connection details
$servername = "localhost"; 
$username = "blog_user";    
$password = "password";    
$sql_store = "blog_12032025";  

try {
    // Create a PDO connection
    $conn = new PDO("mysql:host=$servername;dbname=$sql_store", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if form is submitted
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['title']) && isset($_POST['content'])) {
        $title = htmlspecialchars($_POST['title']);
        $content = htmlspecialchars($_POST['content']);
        $created_at = date('Y-m-d H:i:s'); // Current date and time

        // Insert the new post into the database
        $sql = "INSERT INTO posts (title, content, created_at) VALUES (:title, :content, :created_at)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':created_at', $created_at);

        if ($stmt->execute()) {
            echo "<p>Post added successfully!</p>";
        } else {
            echo "<p>Error adding post.</p>";
        }
    }

    // SQL query to get posts and comments
    $sql = "SELECT posts.post_id, posts.title, posts.content, posts.created_at, comments.comment_id, comments.comment_text, comments.comment_author
            FROM posts
            LEFT JOIN comments ON posts.post_id = comments.post_id
            ORDER BY posts.created_at DESC, comments.created_at ASC";

    // Execute the query
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    // Fetch all results
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Group posts by post_id
    $posts = [];
    foreach ($results as $row) {
        $post_id = $row['post_id'];
        if (!isset($posts[$post_id])) {
            $posts[$post_id] = [
                'title' => $row['title'],
                'content' => $row['content'],
                'created_at' => $row['created_at'],
                'comments' => []
            ];
        }

        // If there is a comment, add it to the post
        if ($row['comment_id'] !== null) {
            $posts[$post_id]['comments'][] = [
                'comment_id' => $row['comment_id'],
                'author' => $row['comment_author'],
                'comment' => $row['comment_text']
            ];
        }
    }

    // Display posts and comments
    foreach ($posts as $post) {
        echo "<h2>" . htmlspecialchars($post['title']) . "</h2>";
        echo "<p>" . nl2br(htmlspecialchars($post['content'])) . "</p>";
        echo "<p><small>Posted on: " . $post['created_at'] . "</small></p>";

        if (count($post['comments']) > 0) {
            echo "<h3>Comments:</h3><ul>";
            foreach ($post['comments'] as $comment) {
                echo "<li><strong>" . $comment['author'] . ":</strong> " . $comment['comment'] . "</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>No comments yet.</p>";
        }
        echo "<hr>";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Close the connection
$conn = null;
?>

<!-- HTML form for submitting a new post -->
<h2>Add New Post</h2>
<form method="POST" action="">
    <label for="title">Title:</label><br>
    <input type="text" id="title" name="title" required><br><br>
    
    <label for="content">Content:</label><br>
    <textarea id="content" name="content" rows="4" cols="50" required></textarea><br><br>
    
    <input type="submit" value="Add Post">
</form>

