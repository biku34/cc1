<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Database connection details
$host = "localhost"; 
$dbname = "collegecupid"; 
$username = "root"; 
$password = ""; 

try {
    // Establish PDO connection
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if form data is set
    if (isset($_POST['feedback'])) {
        $feedback = $_POST['feedback'];
        $user_id = $_SESSION['user_id'];

        // Insert the feedback into the database
        $stmt = $conn->prepare("INSERT INTO feedbacks (user_id, feedback) VALUES (:user_id, :feedback)");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':feedback', $feedback);
        $stmt->execute();
        
        // Redirect after successful submission
        header("Location: feed.php"); 
        exit();
    }

} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}
?>
