<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include the database connection
require_once 'connection.php';

// Check if user is logged in
if (!isset($_SESSION['name']) || empty($_SESSION['name'])) {
    echo '<script>
    Swal.fire({
        title: "Please Login!",
        text: "You need to login to view your profile.",
        icon: "warning",
        confirmButtonText: "OK"
    }).then(() => {
        window.location.href = "signin.php";
    });
    </script>';
    exit();
}

// Get user information
$user_email = $_SESSION['email'];
$user_name = $_SESSION['name'];
$user_gender = $_SESSION['gender'];

// Get user's phone number from database
$user_query = "SELECT phone FROM login WHERE email = '$user_email'";
$user_result = mysqli_query($connection, $user_query);
$user_row = mysqli_fetch_assoc($user_result);
$user_phone = $user_row['phone'] ?? 'Not provided';

// Get user's posted food
$user_food_query = "SELECT * FROM food_donations WHERE email = '$user_email' ORDER BY date DESC";
$user_food_result = mysqli_query($connection, $user_food_query);

// Get user's claimed food
$user_id_query = "SELECT id FROM login WHERE email = '$user_email'";
$user_id_result = mysqli_query($connection, $user_id_query);
$user_id_row = mysqli_fetch_assoc($user_id_result);
$user_id = $user_id_row['id'];

$claimed_food_query = "SELECT * FROM food_donations WHERE assigned_to = '$user_id' ORDER BY date DESC";
$claimed_food_result = mysqli_query($connection, $claimed_food_query);
?> 

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Food Link</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
    <link rel="stylesheet" href="home.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700;800&display=swap');
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        
        .profile-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .profile-header {
            background: linear-gradient(135deg, #8aaee0, #6b9bd2);
            color: white;
            padding: 40px;
            border-radius: 20px;
            margin-bottom: 40px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .profile-header h1 {
            font-size: 2.5rem;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .profile-header p {
            font-size: 1.2rem;
            opacity: 0.9;
        }
        
        .profile-info {
            background: white;
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            border: 1px solid #e9ecef;
        }
        
        .profile-info h2 {
            color: #8aaee0;
            font-size: 1.8rem;
            font-weight: 600;
            border-bottom: 3px solid #8aaee0;
            padding-bottom: 15px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .info-item {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 15px;
            border-left: 5px solid #8aaee0;
        }
        
        .info-item h3 {
            color: #8aaee0;
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .info-item p {
            color: #333;
            font-size: 1.1rem;
            font-weight: 500;
            margin: 0;
        }
        
        .logout-section {
            text-align: center;
            padding-top: 20px;
            border-top: 2px solid #e9ecef;
        }
        
        .logout-btn {
            background: #dc3545;
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
            display: inline-block;
        }
        
        .logout-btn:hover {
            background: #c82333;
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(220, 53, 69, 0.4);
        }
        
        .section {
            background: white;
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            border: 1px solid #e9ecef;
        }
        
        .section h2 {
            color: #8aaee0;
            font-size: 1.8rem;
            font-weight: 600;
            border-bottom: 3px solid #8aaee0;
            padding-bottom: 15px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .food-card {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            border-left: 5px solid #8aaee0;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        
        .food-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        
        .food-card h4 {
            color: #333;
            font-size: 1.4rem;
            font-weight: 600;
            margin-bottom: 15px;
        }
        
        .food-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .food-detail {
            background: white;
            padding: 12px 15px;
            border-radius: 8px;
            border: 1px solid #e9ecef;
            font-size: 14px;
        }
        
        .food-detail strong {
            color: #8aaee0;
            font-weight: 600;
        }
        
        .no-data {
            text-align: center;
            color: #6c757d;
            font-style: italic;
            padding: 60px 20px;
            font-size: 1.1rem;
        }
        
        .no-data i {
            font-size: 4rem;
            color: #8aaee0;
            margin-bottom: 20px;
            display: block;
        }
        
        .claimed-food {
            background: #e3f2fd;
            border-left-color: #2196f3;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="profile-container">
        <!-- Profile Header -->
        <div class="profile-header">
            <h1>My Profile 👤</h1>
            <p>Manage your account information and view your food activities</p>
        </div>

        <!-- Profile Information -->
        <div class="profile-info">
            <h2><i class="fa fa-user"></i> Personal Information</h2>
            <div class="info-grid">
                <div class="info-item">
                    <h3>Name</h3>
                    <p><?php echo htmlspecialchars($user_name); ?></p>
                </div>
                <div class="info-item">
                    <h3>Email</h3>
                    <p><?php echo htmlspecialchars($user_email); ?></p>
                </div>
                <div class="info-item">
                    <h3>Phone Number</h3>
                    <p><?php echo htmlspecialchars($user_phone); ?></p>
                </div>
                <div class="info-item">
                    <h3>Gender</h3>
                    <p><?php echo htmlspecialchars($user_gender); ?></p>
                </div>
            </div>
            <div class="logout-section">
                <a href="logout.php" class="logout-btn">
                    <i class="fa fa-sign-out"></i> Logout
                </a>
            </div>
        </div>          

        <!-- My Posted Food Section -->
        <div class="section">
            <h2><i class="fa fa-upload"></i> My Posted Food</h2>
            <?php if (mysqli_num_rows($user_food_result) > 0): ?>
                <?php while ($food = mysqli_fetch_assoc($user_food_result)): ?>
                    <div class="food-card">
                        <h4><?php echo htmlspecialchars($food['food']); ?></h4>
                        <div class="food-details">
                            <div class="food-detail"><strong>Type:</strong> <?php echo htmlspecialchars($food['type']); ?></div>
                            <div class="food-detail"><strong>Category:</strong> <?php echo htmlspecialchars($food['category']); ?></div>
                            <div class="food-detail"><strong>Quantity:</strong> <?php echo htmlspecialchars($food['quantity']); ?></div>
                            <div class="food-detail"><strong>Location:</strong> <?php echo htmlspecialchars($food['location']); ?></div>
                            <div class="food-detail"><strong>Contact:</strong> <?php echo htmlspecialchars($food['phoneno']); ?></div>
                            <div class="food-detail"><strong>Address:</strong> <?php echo htmlspecialchars($food['address']); ?></div>
                            <div class="food-detail"><strong>Posted:</strong> <?php echo date('M j, Y g:i A', strtotime($food['date'])); ?></div>
                            <div class="food-detail">
                                <strong>Status:</strong> 
                                <?php if ($food['assigned_to'] && $food['assigned_to'] != 0): ?>
                                    <span style="color: #28a745;">✅ Claimed by someone</span>
                                <?php else: ?>
                                    <span style="color: #ffc107;">⏳ Available</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-data">
                    <i class="fa fa-upload"></i>
                    <p>You haven't posted any food yet.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- My Claimed Food Section -->
        <div class="section">
            <h2><i class="fa fa-hand-paper-o"></i> My Claimed Food</h2>
            <?php if (mysqli_num_rows($claimed_food_result) > 0): ?>
                <?php while ($food = mysqli_fetch_assoc($claimed_food_result)): ?>
                    <div class="food-card claimed-food">
                        <h4><?php echo htmlspecialchars($food['food']); ?></h4>
                        <div class="food-details">
                            <div class="food-detail"><strong>Type:</strong> <?php echo htmlspecialchars($food['type']); ?></div>
                            <div class="food-detail"><strong>Category:</strong> <?php echo htmlspecialchars($food['category']); ?></div>
                            <div class="food-detail"><strong>Quantity:</strong> <?php echo htmlspecialchars($food['quantity']); ?></div>
                            <div class="food-detail"><strong>Location:</strong> <?php echo htmlspecialchars($food['location']); ?></div>
                            <div class="food-detail"><strong>Posted by:</strong> <?php echo htmlspecialchars($food['name']); ?></div>
                            <div class="food-detail"><strong>Contact:</strong> <?php echo htmlspecialchars($food['phoneno']); ?></div>
                            <div class="food-detail"><strong>Address:</strong> <?php echo htmlspecialchars($food['address']); ?></div>
                            <div class="food-detail"><strong>Posted:</strong> <?php echo date('M j, Y g:i A', strtotime($food['date'])); ?></div>
                        </div>
                        <span style="background: #28a745; color: white; padding: 8px 16px; border-radius: 20px; font-weight: 600;">
                            <i class="fa fa-check"></i> Claimed by you
                        </span>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-data">
                    <i class="fa fa-hand-paper-o"></i>
                    <p>You haven't claimed any food yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>
