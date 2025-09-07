<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/vendor/autoload.php';
@include __DIR__ . '/mail_secrets.php';

header('Content-Type: application/json');

$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$subject = isset($_POST['subject']) ? trim($_POST['subject']) : 'New Contact Message';
$message = isset($_POST['message']) ? trim($_POST['message']) : '';

if (!$name || !filter_var($email, FILTER_VALIDATE_EMAIL) || !$message) {
    echo json_encode(['success' => false, 'error' => 'Invalid input']);
    exit;
}

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = defined('SMTP_USERNAME_SECRET') ? SMTP_USERNAME_SECRET : 'REPLACE_ME';
    $mail->Password   = defined('SMTP_PASSWORD_SECRET') ? SMTP_PASSWORD_SECRET : 'REPLACE_ME';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    $mail->setFrom($mail->Username, 'FoodLink Contact');
    $mail->addAddress($mail->Username, 'FoodLink Support');
    $mail->addReplyTo($email, $name);

    $mail->isHTML(true);
    $mail->Subject = 'Contact Form: ' . $subject;
    $mail->Body    = '<h3>New contact message</h3>'
                  . '<p><strong>Name:</strong> ' . htmlspecialchars($name) . '</p>'
                  . '<p><strong>Email:</strong> ' . htmlspecialchars($email) . '</p>'
                  . '<p><strong>Subject:</strong> ' . htmlspecialchars($subject) . '</p>'
                  . '<p><strong>Message:</strong><br>' . nl2br(htmlspecialchars($message)) . '</p>';
    $mail->AltBody = "New contact message from $name <$email>\nSubject: $subject\n\n$message";

    $mail->send();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    error_log('PHPMailer Contact Error: ' . $mail->ErrorInfo);
    echo json_encode(['success' => false, 'error' => 'Mailer error']);
}
?>


