<?php
// Start session at the very top
session_start();

// Include the database connection
require_once 'connection.php';

// Check if user is logged in
if (!isset($_SESSION['name']) || empty($_SESSION['name'])) {
    echo '<script>
    Swal.fire({
        title: "Session Expired!",
        text: "Please login again to continue.",
        icon: "warning",
        confirmButtonText: "OK"
    }).then(() => {
        window.location.href = "signin.php";
    });
    </script>';
    exit();
}

// Get user email from session
$user_email = $_SESSION['email'];
$user_name = $_SESSION['name'];

// Handle food claiming
if (isset($_POST['claim_food'])) {
    $food_id = $_POST['food_id'];
    $claimer_email = $_SESSION['email'];
    
    // Get user ID from email
    $user_query = "SELECT id FROM login WHERE email = '$claimer_email'";
    $user_result = mysqli_query($connection, $user_query);
    $user_row = mysqli_fetch_assoc($user_result);
    $claimer_id = $user_row['id'];
    
    // Check if user is trying to claim their own food
    $check_owner = "SELECT email FROM food_donations WHERE Fid = '$food_id' AND email = '$claimer_email'";
    $owner_result = mysqli_query($connection, $check_owner);
    
    if (mysqli_num_rows($owner_result) > 0) {
        echo '<script>
        Swal.fire({
            title: "Cannot Claim Own Food!",
            text: "You cannot claim your own food donation!",
            icon: "error",
            confirmButtonText: "OK"
        });
        </script>';
    } else {
        // Check if food is already claimed
        $check_claimed = "SELECT assigned_to FROM food_donations WHERE Fid = '$food_id'";
        $claimed_result = mysqli_query($connection, $check_claimed);
        $claimed_row = mysqli_fetch_assoc($claimed_result);
        
        if ($claimed_row['assigned_to'] !== null && $claimed_row['assigned_to'] != 0) {
            echo '<script>
            Swal.fire({
                title: "Already Claimed!",
                text: "This food has already been claimed!",
                icon: "error",
                confirmButtonText: "OK"
            });
            </script>';
        } else {
            // Claim the food
            $claim_query = "UPDATE food_donations SET assigned_to = '$claimer_id' WHERE Fid = '$food_id'";
            if (mysqli_query($connection, $claim_query)) {
                // Get food details for the popup
                $food_details_query = "SELECT * FROM food_donations WHERE Fid = '$food_id'";
                $food_details_result = mysqli_query($connection, $food_details_query);
                $food_details = mysqli_fetch_assoc($food_details_result);
                
                // Send email notification to the food donor
                include 'send_email.php';
                $email_sent = sendClaimNotificationEmail(
                    $food_details['email'], // donor email
                    $user_name, // claimer name
                    $claimer_email, // claimer email
                    $food_details // food details
                );
                
                $claim_success = true;
                $claimed_food_name = $food_details['food'];
                $claimed_food_location = $food_details['location'];
                $claimed_food_quantity = $food_details['quantity'];
                $email_notification = $email_sent ? "Email notification sent to donor!" : "Email notification failed to send.";
            } else {
                echo '<script>
                Swal.fire({
                    title: "Error!",
                    text: "Error claiming food. Please try again.",
                    icon: "error",
                    confirmButtonText: "OK"
                });
                </script>';
            }
        }
    }
}

// Get user's posted food
$user_food_query = "SELECT * FROM food_donations WHERE email = '$user_email' ORDER BY date DESC";
$user_food_result = mysqli_query($connection, $user_food_query);

// Get user ID for queries
$user_id_query = "SELECT id FROM login WHERE email = '$user_email'";
$user_id_result = mysqli_query($connection, $user_id_query);
$user_id_row = mysqli_fetch_assoc($user_id_result);
$user_id = $user_id_row['id'];

// Get available food (not posted by current user and not claimed)
$available_food_query = "SELECT * FROM food_donations WHERE email != '$user_email' AND (assigned_to IS NULL OR assigned_to = 0) ORDER BY date DESC";
$available_food_result = mysqli_query($connection, $available_food_query);

// Get claimed food by current user
$claimed_food_query = "SELECT * FROM food_donations WHERE assigned_to = '$user_id' ORDER BY date DESC";
$claimed_food_result = mysqli_query($connection, $claimed_food_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Link Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
    <link rel="stylesheet" href="admin.css">
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
        
        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .welcome-section {
            background: linear-gradient(135deg, #8aaee0, #6b9bd2);
            color: white;
            padding: 40px;
            border-radius: 20px;
            margin-bottom: 40px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .welcome-section h1 {
            font-size: 2.5rem;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .welcome-section p {
            font-size: 1.2rem;
            opacity: 0.9;
        }
        
        .action-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            margin: 30px 0;
            flex-wrap: wrap;
        }
        
        .action-btn {
            background: #8aaee0;
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(138, 174, 224, 0.3);
        }
        
        .action-btn:hover {
            background: #6b9bd2;
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(138, 174, 224, 0.4);
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
        
        .claim-btn {
            background: #28a745;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 25px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
        }
        
        .claim-btn:hover {
            background: #218838;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
        }
        
        .claimed {
            background: #6c757d;
            color: white;
        }
        
        .my-food {
            background: #e3f2fd;
            border-left-color: #2196f3;
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
        
        .search-container {
            margin-bottom: 30px;
        }
        
        .search-input {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }
        
        .search-input:focus {
            outline: none;
            border-color: #8aaee0;
            background: white;
            box-shadow: 0 0 0 3px rgba(138, 174, 224, 0.1);
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="dashboard-container">
        <!-- Welcome Section -->
        <div class="welcome-section">
            <h1>Welcome, <?php echo htmlspecialchars($user_name); ?>! 👋</h1>
            <p>Manage your food donations and find food to claim</p>
        <div class="action-buttons">
            <a href="#available-food" class="action-btn" style="background: #6c757d;">
                <i class="fa fa-utensils"></i> Available Food to Claim
            </a>
            <a href="#my-posted-food" class="action-btn" style="background: #6c757d;">
                <i class="fa fa-upload"></i> Your Foods
            </a>
            <a href="fooddonateform.php" class="action-btn" style="background: #6c757d;">
                <i class="fa fa-plus"></i> Post Food
            </a>
        </div>
        </div>

        <!-- Available Food Section -->
        <div class="section" id="available-food">
            <h2><i class="fa fa-utensils"></i> Available Food to Claim</h2>
            
            <!-- Search Bar -->
            <div class="search-container">
                <input type="text" id="foodSearch" placeholder="Search for food items..." class="search-input">
            </div>
            <?php if (mysqli_num_rows($available_food_result) > 0): ?>
                <?php while ($food = mysqli_fetch_assoc($available_food_result)): ?>
                    <div class="food-card">
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
                        <form method="post" style="display: inline;">
                            <input type="hidden" name="food_id" value="<?php echo $food['Fid']; ?>">
                            <button type="submit" name="claim_food" class="claim-btn">
                                <i class="fa fa-hand-paper-o"></i> Claim This Food
                            </button>
                        </form>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-data">
                    <i class="fa fa-info-circle" style="font-size: 48px; color: #8aaee0;"></i>
                    <p>No food available to claim at the moment.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- My Posted Food Section -->
        <div class="section" id="my-posted-food">
            <h2><i class="fa fa-upload"></i> My Posted Food</h2>
            <?php if (mysqli_num_rows($user_food_result) > 0): ?>
                <?php while ($food = mysqli_fetch_assoc($user_food_result)): ?>
                    <div class="food-card my-food">
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
                    <i class="fa fa-upload" style="font-size: 48px; color: #8aaee0;"></i>
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
                    <i class="fa fa-hand-paper-o" style="font-size: 48px; color: #8aaee0;"></i>
                    <p>You haven't claimed any food yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Smooth scrolling for anchor links with animation
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    // Add smooth scrolling with offset for header
                    const headerHeight = 80; // Approximate header height
                    const targetPosition = target.offsetTop - headerHeight;
                    
                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                    });
                    
                    // Add a subtle highlight effect to the target section
                    target.style.transition = 'box-shadow 0.3s ease';
                    target.style.boxShadow = '0 0 20px rgba(138, 174, 224, 0.5)';
                    
                    setTimeout(() => {
                        target.style.boxShadow = '0 10px 30px rgba(0,0,0,0.08)';
                    }, 2000);
                }
            });
        });
        
        // Auto-refresh page every 30 seconds to show updated data
        setTimeout(function() {
            location.reload();
        }, 30000);

        // Search functionality
        document.getElementById('foodSearch').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const foodCards = document.querySelectorAll('#available-food .food-card');
            
            foodCards.forEach(card => {
                const foodName = card.querySelector('h4').textContent.toLowerCase();
                const foodDetails = card.querySelector('.food-details').textContent.toLowerCase();
                
                if (foodName.includes(searchTerm) || foodDetails.includes(searchTerm)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    </script>

    <?php if (isset($claim_success) && $claim_success): ?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                title: "Food Claimed Successfully! 🎉",
                html: `
                    <div style="text-align: left; padding: 20px;">
                        <h3 style="color: #8aaee0; margin-bottom: 15px;">Claim Details:</h3>
                        <p><strong>Food Item:</strong> <?php echo htmlspecialchars($claimed_food_name); ?></p>
                        <p><strong>Quantity:</strong> <?php echo htmlspecialchars($claimed_food_quantity); ?></p>
                        <p><strong>Location:</strong> <?php echo htmlspecialchars($claimed_food_location); ?></p>
                        <p><strong>Claimed By:</strong> <?php echo htmlspecialchars($user_name); ?></p>
                        <hr style="margin: 15px 0; border: 1px solid #eee;">
                        <p style="color: #28a745; font-size: 0.9rem; background: #d4edda; padding: 10px; border-radius: 5px;">
                            <i class="fa fa-envelope"></i> 
                            Email notification sent to donor!
                        </p>
                        <p style="color: #666; font-size: 0.9rem;">
                            <i class="fa fa-info-circle"></i> 
                            The food donor has been notified and will contact you to arrange pickup within 12-24 hours.
                        </p>
                    </div>
                `,
                icon: "success",
                confirmButtonText: "Got it!",
                confirmButtonColor: "#8aaee0",
                draggable: true,
                allowOutsideClick: false,
                showConfirmButton: true,
                width: '500px'
            });
        });
    </script>
    <?php endif; ?>
</body>
</html>
