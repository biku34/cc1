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

    // Fetch the user's name
    $stmt = $conn->prepare("SELECT name FROM users WHERE id = :id"); // Adjusted to full_name
    $stmt->bindParam(':id', $_SESSION['user_id']);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo "User not found.";
        exit();
    }

    // Fetch feedbacks with user names from the database for the scrolling section
    $stmt = $conn->query("SELECT u.name, f.feedback FROM feedbacks f JOIN users u ON f.user_id = u.id ORDER BY f.created_at DESC LIMIT 5");
    $feedbacks = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
    <title>Feedback and Scrollable Section</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Satisfy', cursive;
            background-color: #ff4d6d;
        }
        .feedback-section {
            padding: 40px;
            background-color: #f8f9fa;
            color: black;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            margin-bottom: 30px;
        }
        .feedback-section h2 {
            text-align: center;
            font-weight: 700;
            margin-bottom: 20px;
            font-size: 2rem;
        }
        .feedback-section form {
            margin-top: 20px;
        }
        .feedback-list {
            height: 300px;
            overflow: hidden;
            margin-top: 30px;
            padding-right: 15px;
            background-color: #ffe6f0;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            position: relative;
        }
        .feedback-items {
            position: absolute;
            top: 0;
            animation: scrollUp 10s linear infinite;
        }
        .feedback-item {
            background: #fff;
            border-radius: 10px;
            margin-bottom: 15px;
            padding: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .feedback-item h5 {
            font-size: 1.2rem;
            margin-bottom: 10px;
            font-weight: 600;
            color: #ff4d6d;
        }
        .feedback-item p {
            font-size: 1rem;
            color: #333;
        }
        .alert {
            font-size: 1.1rem;
            text-align: center;
        }
        .btn-submit {
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
        .btn-submit:hover {
            background-color: #ff3366;
            transform: scale(1.05); 
        }
        .image-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 20px;
        }

        .image-container img {
            max-width: 50%;
        }

        @keyframes scrollUp {
            0% {
                top: 0;
            }
            100% {
                top: -100%;
            }
        }

        /* Two-column layout */
        .row {
            display: flex;
            justify-content: space-between;
        }
        .feedback-form, .feedback-carousel {
            flex: 1;
            margin: 0 15px;
        }

        /* Responsive layout for small screens */
        @media (max-width: 768px) {
            .row {
                flex-direction: column;
            }
            .feedback-form, .feedback-carousel {
                margin: 10px 0;
            }
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <!-- Feedback Section -->
    <div class="feedback-section">
        <h2>We Value Your Feedback</h2>
        <div class="image-container">
                    <img src="bg2.png" alt="Profile Image">
                </div>
        <div class="row">
            <!-- Feedback Form Section -->
            <div class="feedback-form">
                
                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success">Feedback submitted successfully!</div>
                <?php endif; ?>
                <form action="submit_feedback.php" method="POST">
                    <div class="mb-3">
                        <label for="name" class="form-label">Your Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="feedback" class="form-label">Your Feedback</label>
                        <textarea class="form-control" id="feedback" name="feedback" rows="4" placeholder="Share your thoughts..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-submit btn-block">Submit Feedback</button>
                </form>
            </div>

            <!-- Feedback Carousel Section -->
          
            <h2 class="text-center mb-4">What Others Are Saying</h2>
            <div class="image-container">
                    <img src="bg2.png" alt="Profile Image">
                </div>
                <div class="feedback-list">
                    
                    <div class="feedback-items">
                        <?php
                        if ($feedbacks) {
                            foreach ($feedbacks as $feedback) {
                                ?>
                                <div class="feedback-item">
                                    <h5><?php echo htmlspecialchars($feedback['name']); ?></h5>
                                    <p><?php echo htmlspecialchars($feedback['feedback']); ?></p>
                                </div>
                                <?php
                            }
                        } else {
                            echo "<p class='text-center'>No feedback available.</p>";
                        }
                        ?>
                    </div>
                </div><div></div>
    <form action="dashboard.php" method="get">
        <button type="submit" class="btn btn-submit btn-block">Back</button>
    </form>
</div>


        </div>
    </div>
</div>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
