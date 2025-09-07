<?php
// Start session at the very top
session_start();

// Include the correct database connection file
require_once 'connection.php';

// Ensure the user is logged in
if (!isset($_SESSION['name']) || empty($_SESSION['name'])) {
    header("Location: signin.php");
    exit();
}

// Get user email from session
$emailid = $_SESSION['email'];

if (isset($_POST['submit'])) {
    // Check if connection is established
    if (!$connection) {
        die("❌ Database connection error.");
    }

    // Get user input and sanitize
    $foodname = mysqli_real_escape_string($connection, $_POST['foodname']);
    $meal = mysqli_real_escape_string($connection, $_POST['meal']);
    $category = mysqli_real_escape_string($connection, $_POST['image-choice']);
    $quantity = mysqli_real_escape_string($connection, $_POST['quantity']);
    $phoneno = mysqli_real_escape_string($connection, $_POST['phoneno']);
    $district = mysqli_real_escape_string($connection, $_POST['district']);
    $address = mysqli_real_escape_string($connection, $_POST['address']);
    $name = mysqli_real_escape_string($connection, $_POST['name']);

    // Insert data into database
    $query = "INSERT INTO food_donations (email, food, type, category, phoneno, location, address, name, quantity) 
              VALUES ('$emailid', '$foodname', '$meal', '$category', '$phoneno', '$district', '$address', '$name', '$quantity')";

    $query_run = mysqli_query($connection, $query);

    if ($query_run) {
        $success_message = "Food Posted Successfully! 🎉";
        $success_text = "Your food donation has been posted and is now available for others to claim.";
        $redirect_url = "unified_dashboard.php";
    } else {
        $error_message = "Error! ❌";
        $error_text = "Failed to post your food donation. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Food - Food Link</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700;800&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .donate-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            border: 1px solid #e9ecef;
            width: 100%;
            max-width: 600px;
            padding: 40px;
        }
        
        .donate-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .donate-header h1 {
            color: #8aaee0;
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .donate-header p {
            color: #666;
            font-size: 1rem;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            color: #8aaee0;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 0.9rem;
        }
        
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }
        
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #8aaee0;
            background: white;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        .radio-group {
            display: flex;
            gap: 20px;
            margin-top: 10px;
        }
        
        .radio-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .radio-item input[type="radio"] {
            width: auto;
            margin: 0;
        }
        
        .image-radio-group {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-top: 10px;
        }
        
        .image-radio-item {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .image-radio-item input[type="radio"] {
            display: none;
        }
        
        .image-radio-item label {
            cursor: pointer;
            padding: 15px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            transition: all 0.3s ease;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            gap: 15px;
            width: 100%;
        }
        
        .image-radio-item label:hover {
            border-color: #8aaee0;
        }
        
        .image-radio-item input[type="radio"]:checked + label {
            border-color: #8aaee0;
            background: #e3f2fd;
        }
        
        .image-radio-item img {
            width: 100%;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
        }
        
        .section-title {
            color: #8aaee0;
            font-size: 1.2rem;
            font-weight: 600;
            margin: 30px 0 20px 0;
            text-align: center;
            border-bottom: 2px solid #8aaee0;
            padding-bottom: 10px;
        }
        
        .donate-btn {
            width: 100%;
            background: #8aaee0;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }
        
        .donate-btn:hover {
            background: #6b9bd2;
        }
        
        .form-links {
            text-align: center;
        }
        
        .form-links a {
            color: #8aaee0;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        
        .form-links a:hover {
            color: #6b9bd2;
        }
    </style>
</head>
<body>
    <div class="donate-container">
        <div class="donate-header">
            <h1>Post Food</h1>
            <p>Share your surplus food with the community</p>
        </div>
        
        <form method="post">
            <div class="form-group">
                <label for="foodname">Food Name</label>
                <input type="text" id="foodname" name="foodname" required>
            </div>
            
            <div class="form-group">
                <label>Meal Type</label>
                <div class="radio-group">
                    <div class="radio-item">
                        <input type="radio" name="meal" id="veg" value="veg" required>
                        <label for="veg">Vegetarian</label>
                    </div>
                    <div class="radio-item">
                        <input type="radio" name="meal" id="Non-veg" value="Non-veg">
                        <label for="Non-veg">Non-Vegetarian</label>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label>Food Category</label>
                <div class="image-radio-group">
                    <div class="image-radio-item">
                        <input type="radio" id="raw-food" name="image-choice" value="raw-food">
                        <label for="raw-food">
                            <img src="img/raw-food.png" alt="raw-food">
                        </label>
                    </div>
                    <div class="image-radio-item">
                        <input type="radio" id="cooked-food" name="image-choice" value="cooked-food" checked>
                        <label for="cooked-food">
                            <img src="img/cooked-food.png" alt="cooked-food">
                        </label>
                    </div>
                    <div class="image-radio-item">
                        <input type="radio" id="packed-food" name="image-choice" value="packed-food">
                        <label for="packed-food">
                            <img src="img/packed-food.png" alt="packed-food">
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="quantity">Quantity (number of persons/kg)</label>
                <input type="text" id="quantity" name="quantity" required>
            </div>
            
            <div class="section-title">Contact Details</div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" value="<?php echo $_SESSION['name']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="phoneno">Phone Number</label>
                    <input type="tel" id="phoneno" name="phoneno" maxlength="10" pattern="[0-9]{10}" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="district">District</label>
                <select id="district" name="district" required>
                    <option value="">Select District</option>
                    <option value="chennai">Chennai</option>
                    <option value="kancheepuram">Kancheepuram</option>
                    <option value="thiruvallur">Thiruvallur</option>
                    <option value="vellore">Vellore</option>
                    <option value="tiruvannamalai">Tiruvannamalai</option>
                    <option value="tiruppur">Tiruppur</option>
                    <option value="coimbatore">Coimbatore</option>
                    <option value="erode">Erode</option>
                    <option value="salem">Salem</option>
                    <option value="namakkal">Namakkal</option>
                    <option value="tiruchirappalli">Tiruchirappalli</option>
                    <option value="thanjavur">Thanjavur</option>
                    <option value="pudukkottai">Pudukkottai</option>
                    <option value="karur">Karur</option>
                    <option value="ariyalur">Ariyalur</option>
                    <option value="perambalur">Perambalur</option>
                    <option value="cuddalore">Cuddalore</option>
                    <option value="villupuram">Villupuram</option>
                    <option value="tirunelveli">Tirunelveli</option>
                    <option value="thoothukudi">Thoothukudi</option>
                    <option value="tirunelveli">Tirunelveli</option>
                    <option value="kanyakumari">Kanyakumari</option>
                    <option value="madurai">Madurai</option>
                    <option value="theni">Theni</option>
                    <option value="dindigul">Dindigul</option>
                    <option value="ramanathapuram">Ramanathapuram</option>
                    <option value="sivaganga">Sivaganga</option>
                    <option value="virudhunagar">Virudhunagar</option>
                    <option value="tiruppur">Tiruppur</option>
                    <option value="coimbatore">Coimbatore</option>
                    <option value="nilgiris">Nilgiris</option>
                    <option value="erode">Erode</option>
                    <option value="salem">Salem</option>
                    <option value="namakkal">Namakkal</option>
                    <option value="dharmapuri">Dharmapuri</option>
                    <option value="krishnagiri">Krishnagiri</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="address">Address</label>
                <input type="text" id="address" name="address" required>
            </div>
            
            <button type="submit" name="submit" class="donate-btn">
                Post Food
            </button>
            
            <div class="form-links">
                <a href="unified_dashboard.php">← Back to Dashboard</a>
            </div>
        </form>
    </div>

    <?php if (isset($success_message)): ?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                title: "<?php echo $success_message; ?>",
                text: "<?php echo $success_text; ?>",
                icon: "success",
                confirmButtonText: "Continue",
                confirmButtonColor: "#8aaee0",
                draggable: true,
                allowOutsideClick: false,
                showConfirmButton: true,
                timer: 5000,
                timerProgressBar: true
            }).then(() => {
                window.location.href = "<?php echo $redirect_url; ?>";
            });
        });
    </script>
    <?php endif; ?>

    <?php if (isset($error_message)): ?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                title: "<?php echo $error_message; ?>",
                text: "<?php echo $error_text; ?>",
                icon: "error",
                confirmButtonText: "Try Again",
                confirmButtonColor: "#dc3545",
                draggable: true,
                allowOutsideClick: false
            });
        });
    </script>
    <?php endif; ?>
</body>
</html>