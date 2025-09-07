<?php
// Simple email test to verify Gmail SMTP setup

if (isset($_POST['test_email'])) {
    $to = $_POST['to'];
    $subject = "Test Email from FoodLink";
    $message = "
    <html>
    <head>
        <title>Test Email</title>
    </head>
    <body>
        <h2>🎉 Email Test Successful!</h2>
        <p>This is a test email from your FoodLink system.</p>
        <p><strong>Sent at:</strong> " . date('Y-m-d H:i:s') . "</p>
        <p><strong>From:</strong> FoodLink - Food Sharing Platform</p>
        <hr>
        <p>If you received this email, your Gmail SMTP setup is working correctly!</p>
    </body>
    </html>
    ";
    
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: FoodLink - Food Sharing Platform <prawinkrk2003@gmail.com>\r\n";
    
    if (mail($to, $subject, $message, $headers)) {
        $result = "✅ Email sent successfully to: $to";
    } else {
        $result = "❌ Email failed to send to: $to";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Test - FoodLink</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        h1 {
            color: #8aaee0;
            text-align: center;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #333;
        }
        input[type="email"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
        }
        button {
            background: #8aaee0;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
        }
        button:hover {
            background: #6b9bd2;
        }
        .result {
            margin-top: 20px;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
            font-weight: 600;
        }
        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .back-btn {
            display: inline-block;
            background: #6c757d;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="unified_dashboard.php" class="back-btn">← Back to Dashboard</a>
        
        <h1>📧 Email Test</h1>
        <p>Test your Gmail SMTP setup by sending a test email.</p>
        
        <form method="post">
            <div class="form-group">
                <label for="to">Send test email to:</label>
                <input type="email" id="to" name="to" value="prawinkrk2003@gmail.com" required>
            </div>
            
            <button type="submit" name="test_email">Send Test Email</button>
        </form>
        
        <?php if (isset($result)): ?>
            <div class="result <?php echo strpos($result, '✅') !== false ? 'success' : 'error'; ?>">
                <?php echo $result; ?>
            </div>
        <?php endif; ?>
        
        <div style="margin-top: 30px; padding: 20px; background: #e3f2fd; border-radius: 5px;">
            <h3>📋 Setup Checklist:</h3>
            <ul>
                <li>✅ php.ini configured with Gmail SMTP</li>
                <li>✅ sendmail.ini configured with your credentials</li>
                <li>✅ Gmail App Password generated</li>
                <li>🔄 <strong>Test email sending</strong></li>
            </ul>
        </div>
    </div>
</body>
</html>
