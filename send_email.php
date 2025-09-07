<?php
// Email sending functionality using PHPMailer with Gmail SMTP
// This implementation uses PHPMailer for reliable email delivery

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/vendor/autoload.php';
@include __DIR__ . '/mail_secrets.php';

function sendClaimNotificationEmail($donor_email, $claimer_name, $claimer_email, $food_details) {
    // Validate email address first
    if (!filter_var($donor_email, FILTER_VALIDATE_EMAIL)) {
        return false; // Invalid email address
    }
    
    $mail = new PHPMailer(true);
    
    try {
        // SMTP server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = defined('SMTP_USERNAME_SECRET') ? SMTP_USERNAME_SECRET : 'REPLACE_ME';
        $mail->Password   = defined('SMTP_PASSWORD_SECRET') ? SMTP_PASSWORD_SECRET : 'REPLACE_ME';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;  // use STARTTLS
        $mail->Port       = 587;
    
        // Recipients
        $mail->setFrom($mail->Username, 'FoodLink - Food Sharing Platform');
        $mail->addAddress($donor_email);
        $mail->addReplyTo($claimer_email, $claimer_name);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = "Food Claimed - " . $food_details['food'];
        
        $mail->Body = "
        <html>
        <head>
            <title>Food Claimed Notification</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #8aaee0; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f9f9f9; }
                .food-details { background: white; padding: 15px; margin: 15px 0; border-left: 4px solid #8aaee0; }
                .claimer-info { background: #e3f2fd; padding: 15px; margin: 15px 0; border-radius: 5px; }
                .footer { text-align: center; padding: 20px; color: #666; font-size: 0.9rem; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🍽️ Food Claimed!</h1>
                    <p>Someone has claimed your shared food</p>
                </div>
                
                <div class='content'>
                    <h2>Food Details:</h2>
                    <div class='food-details'>
                        <p><strong>Food Item:</strong> " . htmlspecialchars($food_details['food']) . "</p>
                        <p><strong>Type:</strong> " . htmlspecialchars($food_details['type']) . "</p>
                        <p><strong>Category:</strong> " . htmlspecialchars($food_details['category']) . "</p>
                        <p><strong>Quantity:</strong> " . htmlspecialchars($food_details['quantity']) . "</p>
                        <p><strong>Location:</strong> " . htmlspecialchars($food_details['location']) . "</p>
                        <p><strong>Address:</strong> " . htmlspecialchars($food_details['address']) . "</p>
                    </div>
                    
                    <h2>Claimer Information:</h2>
                    <div class='claimer-info'>
                        <p><strong>Name:</strong> " . htmlspecialchars($claimer_name) . "</p>
                        <p><strong>Email:</strong> " . htmlspecialchars($claimer_email) . "</p>
                        <p><strong>Phone:</strong> " . htmlspecialchars($food_details['phoneno']) . "</p>
                        <p><strong>Claimed At:</strong> " . date('F j, Y \a\t g:i A') . "</p>
                    </div>
                    
                    <div style='background: #fff3cd; padding: 15px; border-radius: 5px; margin: 15px 0;'>
                        <h3>📞 Next Steps:</h3>
                        <p>Please contact the claimer to arrange food pickup. Make sure to:</p>
                        <ul>
                            <li>Confirm pickup time and location</li>
                            <li>Verify the claimer's identity</li>
                            <li>Ensure food is still fresh and safe to consume</li>
                        </ul>
                    </div>
                    
                    <div style='background: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; border-radius: 5px; margin: 15px 0;'>
                        <h3 style='color: #721c24; margin-top: 0;'>⏰ Important Notice:</h3>
                        <p style='color: #721c24; margin-bottom: 10px; font-weight: bold;'>
                            Please contact the claimer within 12-24 hours to arrange food pickup.
                        </p>
                        <p style='color: #721c24; margin-bottom: 0;'>
                            If you don't respond within this timeframe, the food order will be automatically cancelled 
                            and made available for other users to claim.
                        </p>
                    </div>
                </div>
                
                <div class='footer'>
                    <p>This email was sent from FoodLink - Connecting communities through food sharing</p>
                    <p>© 2025 FoodLink. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        $mail->AltBody = "Food Claimed: " . $food_details['food'] . " by " . $claimer_name . ". Contact them at " . $claimer_email . " to arrange pickup within 12-24 hours.";
        
        $mail->send();
        return true;
        
    } catch (Exception $e) {
        // Log error for debugging
        error_log("PHPMailer Error: " . $mail->ErrorInfo);
        return false;
    }
}

// Alternative: Using PHPMailer (More reliable)
function sendEmailWithPHPMailer($donor_email, $claimer_name, $claimer_email, $food_details) {
    // This requires PHPMailer library
    // Download from: https://github.com/PHPMailer/PHPMailer
    
    /*
    require_once 'PHPMailer/PHPMailer.php';
    require_once 'PHPMailer/SMTP.php';
    require_once 'PHPMailer/Exception.php';
    
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'your-email@gmail.com';
        $mail->Password = 'your-app-password';
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        
        // Recipients
        $mail->setFrom('noreply@foodlink.com', 'FoodLink');
        $mail->addAddress($donor_email);
        $mail->addReplyTo($claimer_email, $claimer_name);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = '🍽️ Food Claimed - ' . $food_details['food'];
        $mail->Body = $message; // Same HTML message as above
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
    */
}
?>
