<?php
session_start();
require_once 'connection.php';

$status = 'invalid';
if (isset($_GET['token'])) {
    $token = mysqli_real_escape_string($connection, $_GET['token']);
    $res = mysqli_query($connection, "SELECT id, email FROM login WHERE verification_token = '$token' AND verified = 0");
    if ($res && mysqli_num_rows($res) === 1) {
        $row = mysqli_fetch_assoc($res);
        $uid = (int)$row['id'];
        mysqli_query($connection, "UPDATE login SET verified = 1, verification_token = NULL, verified_at = NOW() WHERE id = $uid");
        $status = 'success';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Verification - FoodLink</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<script>
<?php if ($status === 'success'): ?>
    Swal.fire({
        title: 'Account Verified!',
        text: 'Your email has been verified. You can now sign in.',
        icon: 'success',
        confirmButtonText: 'Go to Sign In'
    }).then(() => { window.location.href = 'signin.php'; });
<?php else: ?>
    Swal.fire({
        title: 'Invalid or Expired Link',
        text: 'The verification link is invalid or has already been used.',
        icon: 'error',
        confirmButtonText: 'Go to Home'
    }).then(() => { window.location.href = 'index.html'; });
<?php endif; ?>
</script>
</body>
</html>


