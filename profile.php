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

    // Fetch the logged-in user's data
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->bindParam(':id', $_SESSION['user_id']);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo "User not found.";
        exit();
    }

    // Fetch the logged-in user's gender
    $user_gender = $user['gender']; // This assumes 'gender' is stored in the 'users' table

    // Fetch other users' data excluding the logged-in user and filter by opposite gender
    if ($user_gender == 'Male') {
        // Show female profiles to male users
        $stmt = $conn->prepare("SELECT id, name, university, age, gender, bio, music_pref, movie_pref, hobby, reading_pref, partner_pref FROM users WHERE id != :id AND gender = 'Female'");
    } else {
        // Show male profiles to female users
        $stmt = $conn->prepare("SELECT id, name, university, age, gender, bio, music_pref, movie_pref, hobby, reading_pref, partner_pref FROM users WHERE id != :id AND gender = 'Male'");
    }

    $stmt->bindParam(':id', $_SESSION['user_id']);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$users) {
        echo "No other users found.";
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
        .Welcome {
            font-family: 'Satisfy', cursive; /* Same font as the navbar brand */
        }
        .dashboard-container h2 {
            color: #ff4d6d;
            font-weight: 700;
            text-align: center;
            margin-bottom: 20px;
        }
        .user-info p {
            font-size: 1rem;
            color: #333;
        }
        .btn-custom {
            background-color: #ff4d6d;
            color: #fff;
            font-weight: 600;
            border: none;
            border-radius: 25px;
            padding: 10px 20px;
            font-size: 1rem;
            transition: background-color 0.3s ease;
        }
        .btn-custom:hover {
            background-color: #ff3366;
        }
        .carousel-item img {
            max-height: 300px;
            object-fit: cover;
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
        }
        
        /* Custom styling for black carousel arrows */
        .carousel-control-prev-icon,
        .carousel-control-next-icon {
            background-color: black; /* Color of the arrows */
            border-radius: 50%; /* Make the arrows circular */
        }

        .carousel-control-prev:hover,
        .carousel-control-next:hover {
            background-color: rgba(0, 0, 0, 0.2); /* Slight hover effect */
        }

        .carousel-control-prev,
        .carousel-control-next {
            color: black; /* Arrow color */
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
        .btn-back {
            background-color: #ff4d6d;
            color: #fff;
            font-weight: 600;
            border: none;
            border-radius: 25px;
            padding: 10px 20px;
            font-size: 1rem;
            transition: background-color 0.3s ease;
        }
        .btn-back:hover {
            background-color: #ff3366;
        } 
        .but{
            display: flex;
            justify-content: center; /* Center the button horizontally */
            margin-top: 20px; /* Adds some space above the button */
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
        <h2 class="Welcome">Welcome, Let's match ?</h2>
        <div class="image-container">
            <img src="bg2.png" alt="Profile Image">
        </div>

        <!-- Profile Carousel -->
        <div id="profileCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <?php
                if ($users) {
                    $first = true;
                    foreach ($users as $otherUser) {
                        // Check if it's the first profile to add the "active" class
                        $activeClass = $first ? 'active' : '';
                        $first = false;
                ?>
                    <div class="carousel-item <?php echo $activeClass; ?>">
                        <div class="profile-detail text-center">
                            <h5><?php echo htmlspecialchars($otherUser['name']); ?></h5>
                            <p><strong>University:</strong> <?php echo htmlspecialchars($otherUser['university']); ?></p>
                            <p><strong>Age:</strong> <?php echo htmlspecialchars($otherUser['age']); ?></p>
                            <p><strong>Gender:</strong> <?php echo htmlspecialchars($otherUser['gender']); ?></p>
                            <p><strong>Bio:</strong> <?php echo htmlspecialchars($otherUser['bio']); ?></p>
                            <p><strong>Music Preferences:</strong> <?php echo htmlspecialchars($otherUser['music_pref']); ?></p>
                            <p><strong>Movie Preferences:</strong> <?php echo htmlspecialchars($otherUser['movie_pref']); ?></p>
                            <p><strong>Hobbies:</strong> <?php echo htmlspecialchars($otherUser['hobby']); ?></p>
                            <p><strong>Reading Preferences:</strong> <?php echo htmlspecialchars($otherUser['reading_pref']); ?></p>
                            <p><strong>Partner Preferences:</strong> <?php echo htmlspecialchars($otherUser['partner_pref']); ?></p>
                            <form method="POST" action="like_profile.php" class="d-inline">
                                <input type="hidden" name="receiver_id" value="<?php echo htmlspecialchars($otherUser['id']); ?>">
                                <button type="submit" class="btn btn-custom">Like</button>
                            </form>
                        </div>
                    </div>
                <?php 
                    }
                } else {
                    echo "<p>No other users available.</p>";
                }
                ?>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#profileCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#profileCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
        <div class="but">
            <a href="dashboard.php" class="btn btn-back">Back</a>
        </div>
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
