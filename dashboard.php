<?php
// Start session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Database connection
$host = "localhost"; // Replace with your host
$dbname = "collegecupid"; // Replace with your database name
$username = "root"; // Replace with your database username
$password = ""; // Replace with your database password

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch user data
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->bindParam(':id', $_SESSION['user_id']);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo "User not found.";
        exit();
    }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - CollegeCupid</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&family=Satisfy&display=swap" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&family=Dancing+Script&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #ff4d6d;
            color: #fff;
            margin: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .navbar {
            background-color: #ff4d6d;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            position: sticky;
            top: 0;
            width: 100%;
        }
        .navbar-brand {
            font-weight: 700;
            color: #fff !important;
            font-size: 1.8rem;
            text-transform: uppercase;
        }
        .dashboard-container {
            margin: 20px auto;
            max-width: 800px;
            background: #fff;
            color: #333;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            
        }
        .Welcome{
            font-family: 'Satisfy', cursive; /* Same font as the navbar brand */
        }
        .dashboard-container h2 {
            color: #ff4d6d;
            font-weight: 700;
            text-align: center;
            margin-bottom: 20px;
        }
        .button-container {
            display: flex;
            justify-content: space-between;
            gap: 15px; /* Add gap between buttons */
            margin-bottom: 20px; /* Add some space below the button rows */
        }

        .top-buttons {
            flex-direction: row; /* Align buttons horizontally */
            justify-content: center; /* Center-align top buttons */
        }

        .bottom-buttons {
            flex-direction: row; /* Align buttons horizontally */
            justify-content: center; /* Center-align bottom buttons */
        }

        .btn-custom {
            background-color: #ff4d6d;
            color: #fff;
            font-weight: 600;
            border: none;
            border-radius: 25px;
            padding: 12px 25px;
            font-size: 1.1rem;
            transition: background-color 0.3s ease, transform 0.2s ease;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .btn-custom:hover {
            background-color: #ff3366;
            transform: scale(1.05); 
        }


        .profile-detail {
            margin-bottom: 15px;
        }
        .footer {
            background-color: #333;
            color: #fff;
            text-align: center;
            font-size: 0.9rem;
            padding: 15px 0;
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
        }
        .footer.fixed-bottom {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
        }
        .image-container {
            display: flex;
            justify-content: center; /* Aligns the image horizontally in the center */
            align-items: center; /* Aligns the image vertically in the center */
            margin-bottom: 20px; /* Adds space below the image */
        }

        .image-container img {
            max-width: 50%; /* Ensures the image doesn't overflow its container */ /* Maintains the aspect ratio */
        }


    #feedbackBtn {
        position: fixed;
        bottom: 20px;
        right: 20px;
        background-color: #ff4d6d;
        color: #fff;
        border: none;
        padding: 15px 20px;
        border-radius: 50%;
        font-size: 1.5rem;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        transition: background-color 0.3s ease, transform 0.3s ease;
        z-index: 9999;
    }

    #feedbackBtn:hover {
        background-color: #ff3366;
        transform: scale(1.1);
    }

    /* Icon inside the button */
    #feedbackBtn i {
        font-size: 1.2rem;
    }

    /* Mobile Responsiveness */
    @media (max-width: 768px) {
        #feedbackBtn {
            bottom: 15px;
            right: 15px;
            padding: 12px 15px;
        }
    }

    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="#">CollegeCupid</a>
            <div class="ms-auto">
                <form action="logout.php" method="POST">
                    <button type="submit" class="btn btn-custom">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Dashboard -->
    <div class="dashboard-container">
        <h2 class="Welcome">Welcome, <?php echo htmlspecialchars($user['name']); ?>!</h2>
        <div class="image-container">
            <img src="bg2.png" alt="Profile Image">
        </div>
        <div class="profile-detail"><strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?></div>
        <div class="profile-detail"><strong>Age:</strong> <?php echo htmlspecialchars($user['age']); ?></div>
        <div class="profile-detail"><strong>University:</strong> <?php echo htmlspecialchars($user['university']); ?></div>
        <div class="profile-detail"><strong>Bio:</strong> <?php echo htmlspecialchars($user['bio']); ?></div>

        <!-- Music Preferences -->
        <div class="profile-detail">
            <strong>Music Preferences:</strong> 
            <?php echo isset($user['music_pref']) && !empty($user['music_pref']) ? htmlspecialchars($user['music_pref']) : "Not provided."; ?>
        </div>

        <!-- Movie Preferences -->
        <div class="profile-detail">
            <strong>Movie Preferences:</strong> 
            <?php echo isset($user['movie_pref']) && !empty($user['movie_pref']) ? htmlspecialchars($user['movie_pref']) : "Not provided."; ?>
        </div>

        <!-- Hobbies -->
        <div class="profile-detail">
            <strong>Hobbies:</strong> 
            <?php echo isset($user['hobby']) && !empty($user['hobby']) ? htmlspecialchars($user['hobby']) : "Not provided."; ?>
        </div>

        <!-- Reading Preferences -->
        <div class="profile-detail">
            <strong>Reading Preferences:</strong> 
            <?php echo isset($user['reading_pref']) && !empty($user['reading_pref']) ? htmlspecialchars($user['reading_pref']) : "Not provided."; ?>
        </div>

        <!-- Partner Preferences -->
        <div class="profile-detail">
            <strong>Partner Preferences:</strong> 
            <?php echo isset($user['partner_pref']) && !empty($user['partner_pref']) ? htmlspecialchars($user['partner_pref']) : "Not provided."; ?>
        </div>

        <!-- Top buttons -->
        <div class="button-container top-buttons">
            <a href="likes.php" class="btn btn-custom">Liked By</a>
            <a href="profile.php" class="btn btn-custom">View Profiles</a>
        </div>

        <!-- Bottom buttons -->
        <div class="button-container bottom-buttons">
            <a href="edit_profile.php" class="btn btn-custom">Edit Profile</a>
            <a href="mutual.php" class="btn btn-custom">Mutual Likes</a>
        </div>

        <button id="feedbackBtn" class="btn btn-feedback" onclick="window.location.href='feed.php';">
    <i class="bi bi-pencil"></i> Feedback
</button>

    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 Bikram Sadhukhan. All Rights Reserved.</p>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
