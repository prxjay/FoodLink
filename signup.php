<?php
session_start();
include 'connection.php';
@include __DIR__ . '/mail_secrets.php';

$msg = 0;
if (isset($_POST['sign'])) {
    $name = mysqli_real_escape_string($connection, $_POST['name']);
    $email = mysqli_real_escape_string($connection, $_POST['email']);
    $phone = mysqli_real_escape_string($connection, $_POST['phone']);
    $password = mysqli_real_escape_string($connection, $_POST['password']);
    $gender = mysqli_real_escape_string($connection, $_POST['gender']);

    // Check if email already exists
    $check_email = "SELECT * FROM login WHERE email = '$email'";
    $result = mysqli_query($connection, $check_email);
    
    if (mysqli_num_rows($result) > 0) {
        $msg = 1; // Email already exists
    } else {
        // Prepare OTP flow: do not create DB record yet
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $otp = random_int(100000, 999999);
        $otp_expires_at = time() + 15 * 60; // 15 minutes

        $_SESSION['pending_signup'] = [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'password' => $hashed_password,
            'gender' => $gender,
            'otp' => $otp,
            'expires' => $otp_expires_at
        ];

        // Send OTP via PHPMailer
        require_once 'vendor/autoload.php';
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = defined('SMTP_USERNAME_SECRET') ? SMTP_USERNAME_SECRET : 'REPLACE_ME';
            $mail->Password   = defined('SMTP_PASSWORD_SECRET') ? SMTP_PASSWORD_SECRET : 'REPLACE_ME';
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom($mail->Username, 'FoodLink');
            $mail->addAddress($email, $name);
            $mail->isHTML(true);
            $mail->Subject = 'Your FoodLink OTP (valid for 15 minutes)';
            $mail->Body    = '<h2>OTP Verification</h2>'
                . '<p>Hello ' . htmlspecialchars($name) . ',</p>'
                . '<p>Your OTP is: <strong style="font-size:20px;">' . $otp . '</strong></p>'
                . '<p>This code is valid for 15 minutes.</p>';
            $mail->AltBody = 'Your FoodLink OTP is ' . $otp . ' (valid 15 minutes).';
            $mail->send();

            header('Location: verify_otp.php');
            exit;
        } catch (Exception $e) {
            $msg = 5; // Email failed
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Food Link</title>
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
        
        .signup-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            border: 1px solid #e9ecef;
            width: 100%;
            max-width: 450px;
            padding: 40px;
        }
        
        .signup-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .signup-header h1 {
            color: #8aaee0;
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .signup-header p {
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
        
        .signup-btn {
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
        
        .signup-btn:hover {
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
        
        .signin-link {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
        }
        
        .signin-link p {
            color: #666;
            font-size: 0.9rem;
        }
        
        .signin-link a {
            color: #8aaee0;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }
        
        .signin-link a:hover {
            color: #6b9bd2;
        }
    </style>
</head>
<body>
    <div class="signup-container">
        <div class="signup-header">
            <h1>Sign Up</h1>
            <p>Create your FoodLink account</p>
                </div>

        <form method="post">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" required>
                </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required>
                </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" required>
                </div>

                <div class="form-group">
                    <label for="gender">Gender</label>
                    <select id="gender" name="gender" required>
                        <option value="">Select Gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
                </div>

            <button type="submit" name="sign" class="signup-btn">
                Create Account
            </button>
            
            <div class="form-links">
                    <a href="index.html">← Return to Home</a>
                </div>

            <div class="signin-link">
                <p>Already have an account? <a href="signin.php">Sign In</a></p>
                </div>
            </form>
    </div>

    <script>
        // Handle error messages with SweetAlert2
        <?php if ($msg == 1): ?>
            Swal.fire({
                title: "Email Already Exists!",
                text: "An account with this email address already exists. Please use a different email or try signing in.",
                icon: "warning",
                confirmButtonText: "OK",
                confirmButtonColor: "#8aaee0"
            });
        <?php elseif ($msg == 2): ?>
            Swal.fire({
                title: "Registration Successful!",
                text: "Your account has been created successfully. You can now sign in to your account.",
                icon: "success",
                confirmButtonText: "OK",
                confirmButtonColor: "#8aaee0"
            }).then(() => {
                window.location.href = "signin.php";
            });
        <?php elseif ($msg == 3): ?>
            Swal.fire({
                title: "Registration Failed!",
                text: "Something went wrong during registration. Please try again.",
                icon: "error",
                confirmButtonText: "OK",
                confirmButtonColor: "#8aaee0"
            });
        <?php endif; ?>
    </script>
</body>
</html>