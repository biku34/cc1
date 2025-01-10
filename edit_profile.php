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

    // Update user data if form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Validate and sanitize inputs
        $age = filter_var($_POST['age'], FILTER_VALIDATE_INT);
        if ($age < 18 || $age > 25) {
            $error = "Age must be between 18 and 25.";
        }

        $bio = htmlspecialchars($_POST['bio']);
        if (str_word_count($bio) > 10) {
            $error = "Bio should not exceed 10 words.";
        }

        // Prevent XSS by sanitizing the preferences fields
        $music_preferences = htmlspecialchars($_POST['music_preferences']);
        $movie_preferences = htmlspecialchars($_POST['movie_preferences']);
        $hobbies = htmlspecialchars($_POST['hobbies']);
        $reading_preferences = htmlspecialchars($_POST['reading_preferences']);
        $partner_preferences = htmlspecialchars($_POST['partner_preferences']);

        if (!isset($error)) {
            // Update user profile in database
            $stmt = $conn->prepare("UPDATE users SET age = :age, bio = :bio, music_pref = :music_preferences, movie_pref = :movie_preferences, hobby = :hobbies, reading_pref = :reading_preferences, partner_pref = :partner_preferences WHERE id = :id");
            $stmt->bindParam(':age', $age);
            $stmt->bindParam(':bio', $bio);
            $stmt->bindParam(':music_preferences', $music_preferences);
            $stmt->bindParam(':movie_preferences', $movie_preferences);
            $stmt->bindParam(':hobbies', $hobbies);
            $stmt->bindParam(':reading_preferences', $reading_preferences);
            $stmt->bindParam(':partner_preferences', $partner_preferences);
            $stmt->bindParam(':id', $_SESSION['user_id']);
            $stmt->execute();
            $success = "Profile updated successfully!";
        }
    }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}

// Music, movie, reading options (example)
$music_options = ["Rock", "Pop", "Jazz", "Classical", "Hip-Hop", "Electronic", "Blues", "Reggae", "Country", "R&B", "Metal", "Folk", "Indie", "Alternative", "Punk", "Soul", "Disco", "Techno", "House", "Trap", "K-Pop", "Latin", "Acoustic", "EDM", "Funk"];
$movie_options = ["Action", "Comedy", "Drama", "Thriller", "Romance", "Horror", "Sci-Fi", "Documentary", "Adventure", "Fantasy", "Crime", "Animation", "Musical", "Family", "Mystery", "Historical", "War", "Sports", "Western", "Romantic Comedy", "Documentary", "Biographical", "Action-Comedy", "Action-Adventure", "Action-Drama"];
$reading_options = ["Fiction", "Non-Fiction", "Biography", "Science Fiction", "Fantasy", "Historical", "Mystery", "Thriller", "Self-Help", "Philosophy", "Psychology", "Health", "Romance", "Adventure", "Poetry", "Travel", "Science", "Art", "Politics", "Business", "Technology", "Spirituality", "Religion", "Literature", "Cooking"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - CollegeCupid</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
        .edit-profile-container {
            margin: 20px auto;
            max-width: 800px;
            background: #fff;
            color: #333;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        .edit-profile-container h2 {
            color: #ff4d6d;
            font-weight: 700;
            text-align: center;
            margin-bottom: 20px;
            font-family: 'Satisfy', cursive;
        }
        .btn-custom, .btn-back {
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
        .profile-detail {
            margin-bottom: 15px;
        }
        .footer {
            background-color: #333;
            color: #fff;
            text-align: center;
            font-size: 0.9rem;
            padding: 15px 0;
        }
        .btn-back:hover {
            background-color: #ff3366;
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

    <!-- Edit Profile Form -->
    <div class="edit-profile-container">
        <h2>Edit Your Profile</h2>
        <div class="image-container">
            <img src="bg2.png" alt="Profile Image">
        </div>
        <?php if (isset($error)) { echo "<div class='alert alert-danger'>$error</div>"; } ?>
        <?php if (isset($success)) { echo "<div class='alert alert-success'>$success</div>"; } ?>

        <form method="POST">
            <div class="mb-3">
                <label for="age" class="form-label">Age (18-25)</label>
                <input type="number" class="form-control" id="age" name="age" value="<?php echo htmlspecialchars($user['age']); ?>" min="18" max="25" required>
            </div>

            <div class="mb-3">
                <label for="bio" class="form-label">Bio (max 10 words)</label>
                <textarea class="form-control" id="bio" name="bio" rows="3" required><?php echo htmlspecialchars($user['bio']); ?></textarea>
            </div>

            <div class="mb-3">
                <label for="music_preferences" class="form-label">Music Preferences</label>
                <select class="form-control" id="music_preferences" name="music_preferences">
                    <?php foreach ($music_options as $music) { ?>
                        <option value="<?php echo htmlspecialchars($music); ?>" <?php echo ($user['music_pref'] == $music) ? 'selected' : ''; ?>><?php echo htmlspecialchars($music); ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="movie_preferences" class="form-label">Movie Preferences</label>
                <select class="form-control" id="movie_preferences" name="movie_preferences">
                    <?php foreach ($movie_options as $movie) { ?>
                        <option value="<?php echo htmlspecialchars($movie); ?>" <?php echo ($user['movie_pref'] == $movie) ? 'selected' : ''; ?>><?php echo htmlspecialchars($movie); ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="reading_preferences" class="form-label">Reading Preferences</label>
                <select class="form-control" id="reading_preferences" name="reading_preferences">
                    <?php foreach ($reading_options as $reading) { ?>
                        <option value="<?php echo htmlspecialchars($reading); ?>" <?php echo ($user['reading_pref'] == $reading) ? 'selected' : ''; ?>><?php echo htmlspecialchars($reading); ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="hobbies" class="form-label">Hobbies</label>
                <textarea class="form-control" id="hobbies" name="hobbies" rows="3"><?php echo htmlspecialchars($user['hobby']); ?></textarea>
            </div>

            <div class="mb-3">
                <label for="partner_preferences" class="form-label">Partner Preferences</label>
                <textarea class="form-control" id="partner_preferences" name="partner_preferences" rows="3"><?php echo htmlspecialchars($user['partner_pref']); ?></textarea>
            </div>

            <button type="submit" class="btn btn-custom">Save Changes</button>
            <a href="dashboard.php" class="btn btn-back">Back</a>
        </form>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 Bikram Sadhukhan. All Rights Reserved.</p>
        </div>
    </footer>
</body>
</html>
