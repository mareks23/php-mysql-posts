<?php
 if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $virsrakst = $_POST['virsraksts'];
    $comment = $_POST['comment'];
    echo "hi, " . htmlspecialchars($name) . "<br>";
    echo "Virsraksts: " . htmlspecialchars($virsrakst). "<br>";
    echo "Tavs koments: " . htmlspecialchars($comment) . "<br>";
}
?>