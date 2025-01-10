<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $contact = $_POST['contact'];
    $university = $_POST['university'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];

    $sql = "INSERT INTO users (name, email, password, contact, university, age, gender, bio, music_pref, movie_pref, hobby, reading_pref, partner_pref)
            VALUES ('$name', '$email', '$password', '$contact', '$university', $age, '$gender', NULL, NULL, NULL, NULL, NULL, NULL)";

    if ($conn->query($sql) === TRUE) {
        // Redirect to index.html after successful registration
        header("Location: index.html");
        exit(); // Make sure no further code is executed after the redirect
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>
