<?php
session_start();
require_once 'connection.php';

$msg = 0;

// If OTP submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $entered_otp = isset($_POST['otp']) ? trim($_POST['otp']) : '';
    if (!isset($_SESSION['pending_signup'])) {
        $msg = 3; // Session expired
    } else {
        $pending = $_SESSION['pending_signup'];
        if (time() > $pending['expires']) {
            unset($_SESSION['pending_signup']);
            $msg = 2; // Expired
        } elseif ($entered_otp === (string)$pending['otp']) {
            // Create account now
            $name = mysqli_real_escape_string($connection, $pending['name']);
            $email = mysqli_real_escape_string($connection, $pending['email']);
            $phone = mysqli_real_escape_string($connection, $pending['phone']);
            $password = mysqli_real_escape_string($connection, $pending['password']);
            $gender = mysqli_real_escape_string($connection, $pending['gender']);

            $insert_query = "INSERT INTO login (name, email, phone, password, gender) VALUES ('$name', '$email', '$phone', '$password', '$gender')";
            if (mysqli_query($connection, $insert_query)) {
                unset($_SESSION['pending_signup']);
                $msg = 4; // Verified and created
            } else {
                $msg = 5; // DB error
            }
        } else {
            $msg = 1; // Wrong OTP
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
    <title>Verify OTP - FoodLink</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700;800&display=swap');
        body { font-family: 'Poppins', sans-serif; background: #f8f9fa; margin: 0; padding: 0; }
        .container { max-width: 420px; margin: 60px auto; background: #fff; border-radius: 20px; border: 1px solid #e9ecef; box-shadow: 0 10px 30px rgba(0,0,0,0.08); padding: 32px; }
        h1 { color: #8aaee0; font-size: 1.8rem; margin: 0 0 10px; text-align: center; }
        p { color: #666; text-align: center; margin-bottom: 20px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; color: #8aaee0; font-weight: 600; margin-bottom: 8px; }
        input { width: 100%; padding: 12px 15px; border: 2px solid #e9ecef; border-radius: 10px; font-size: 1rem; }
        .btn { width: 100%; background: #8aaee0; color: #fff; padding: 12px; border: none; border-radius: 10px; font-size: 1.1rem; font-weight: 600; cursor: pointer; }
        .btn:hover { background: #6b9bd2; }
        .link { text-align: center; margin-top: 16px; }
        .link a { color: #8aaee0; text-decoration: none; font-weight: 600; }
    </style>
    </head>
<body>
    <div class="container">
        <h1>Verify OTP</h1>
        <p>Enter the 6-digit OTP sent to your email. It expires in 15 minutes.</p>
        <form method="post">
            <div class="form-group">
                <label for="otp">OTP</label>
                <input type="text" id="otp" name="otp" pattern="\d{6}" maxlength="6" required>
            </div>
            <button type="submit" class="btn">Verify</button>
        </form>
        <div class="link"><a href="signup.php">Change email</a></div>
    </div>

    <script>
        <?php if ($msg === 1): ?>
            Swal.fire({ title: 'Invalid OTP', text: 'The OTP you entered is incorrect.', icon: 'error', confirmButtonText: 'OK' });
        <?php elseif ($msg === 2): ?>
            Swal.fire({ title: 'OTP Expired', text: 'Please sign up again to receive a new OTP.', icon: 'warning', confirmButtonText: 'OK' });
        <?php elseif ($msg === 3): ?>
            Swal.fire({ title: 'Session Expired', text: 'Please sign up again to receive a new OTP.', icon: 'warning', confirmButtonText: 'OK' });
        <?php elseif ($msg === 4): ?>
            Swal.fire({ title: 'Account Created!', text: 'Your email is verified and account created. You can sign in now.', icon: 'success', confirmButtonText: 'Go to Sign In' }).then(() => { window.location.href = 'signin.php'; });
        <?php elseif ($msg === 5): ?>
            Swal.fire({ title: 'Registration Failed', text: 'Could not create your account. Please try again.', icon: 'error', confirmButtonText: 'OK' });
        <?php endif; ?>
    </script>
</body>
</html>


