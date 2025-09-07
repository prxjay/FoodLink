<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
$is_logged_in = isset($_SESSION['name']) && !empty($_SESSION['name']);
$user_name = $is_logged_in ? $_SESSION['name'] : '';
?>
<header>
    <div class="logo">Food<b style="color: #8aaee0;">Link</b></div>
    <div class="hamburger">
        <div class="line"></div>
        <div class="line"></div>
        <div class="line"></div>
    </div>
    <nav class="nav-bar">
        <ul>
            <li><a href="index.html" <?php echo (basename($_SERVER['PHP_SELF']) == 'index.html') ? 'class="active"' : ''; ?>>Home</a></li>
            <li><a href="<?php echo $is_logged_in ? 'unified_dashboard.php' : 'signin.php'; ?>" <?php echo (basename($_SERVER['PHP_SELF']) == 'signin.php' || basename($_SERVER['PHP_SELF']) == 'unified_dashboard.php') ? 'class="active"' : ''; ?>>View Foods</a></li>
            <li><a href="about.html" <?php echo (basename($_SERVER['PHP_SELF']) == 'about.html') ? 'class="active"' : ''; ?>>About</a></li>
            <li><a href="contact.html" <?php echo (basename($_SERVER['PHP_SELF']) == 'contact.html') ? 'class="active"' : ''; ?>>Contact</a></li>
            <li><a href="<?php echo $is_logged_in ? 'profile.php' : 'signin.php'; ?>" <?php echo (basename($_SERVER['PHP_SELF']) == 'profile.php') ? 'class="active"' : ''; ?>>Profile</a></li>
        </ul>
    </nav>
</header>

<script>
    document.querySelector(".hamburger").onclick = function () {
        document.querySelector(".nav-bar").classList.toggle("active");
    }
</script>
