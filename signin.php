<?php
session_start();
include 'connection.php';

$msg = 0;
if (isset($_POST['sign'])) {
    $email = mysqli_real_escape_string($connection, $_POST['email']);
    $password = mysqli_real_escape_string($connection, $_POST['password']);

    $sql = "select * from login where email='$email'";
    $result = mysqli_query($connection, $sql);
    $num = mysqli_num_rows($result);

    if ($num == 1) {
        while ($row = mysqli_fetch_assoc($result)) {
            if (password_verify($password, $row['password'])) {
                $_SESSION['email'] = $email;
                $_SESSION['name'] = $row['name'];
                $_SESSION['gender'] = $row['gender'];
                header("location:unified_dashboard.php");
            } else {
                $msg = 2; // Wrong password
            }
        }
    } else {
        $msg = 3; // Account doesn't exist
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - Food Link</title>
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
        
        .login-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            border: 1px solid #e9ecef;
            width: 100%;
            max-width: 400px;
            padding: 40px;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .login-header h1 {
            color: #8aaee0;
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .login-header p {
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
        
        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #8aaee0;
            background: white;
        }
        
        .login-btn {
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
        
        .login-btn:hover {
            background: #6b9bd2;
        }
        
        .form-links {
            text-align: center;
            margin-bottom: 20px;
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
        
        .signup-link {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
        }
        
        .signup-link p {
            color: #666;
            font-size: 0.9rem;
        }
        
        .signup-link a {
            color: #8aaee0;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }
        
        .signup-link a:hover {
            color: #6b9bd2;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>Sign In</h1>
            <p>Welcome back to FoodLink</p>
        </div>
        
        <form method="post">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" name="sign" class="login-btn">
                Sign In
            </button>
            
            <div class="form-links">
                <a href="index.html">← Return to Home</a>
            </div>
            
            <div class="signup-link">
                <p>Don't have an account? <a href="signup.php">Register Now</a></p>
            </div>
        </form>
    </div>

    <script>
        // Handle error messages with SweetAlert2
        <?php if ($msg == 2): ?>
            Swal.fire({
                title: "Wrong Password!",
                text: "The password you entered is incorrect. Please try again.",
                icon: "error",
                confirmButtonText: "OK",
                confirmButtonColor: "#8aaee0"
            });
        <?php elseif ($msg == 3): ?>
            Swal.fire({
                title: "Account Not Found!",
                text: "No account found with this email address. Please check your email or register a new account.",
                icon: "warning",
                confirmButtonText: "OK",
                confirmButtonColor: "#8aaee0"
            });
        <?php elseif ($msg == 4): ?>
            Swal.fire({
                title: "Email Not Verified",
                text: "Please check your email and verify your account before signing in.",
                icon: "info",
                confirmButtonText: "OK",
                confirmButtonColor: "#8aaee0"
            });
        <?php endif; ?>
    </script>
</body>
</html>