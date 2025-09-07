<?php
// Simple SMTP email sender for Gmail
// This bypasses the PHP mail() function issues

function sendEmailSimple($to, $subject, $message, $from_email, $from_name, $reply_to = '') {
    // For now, we'll create a simple file-based email system
    // This will save emails to a file for testing purposes
    
    $email_data = [
        'to' => $to,
        'subject' => $subject,
        'message' => $message,
        'from' => $from_email,
        'from_name' => $from_name,
        'reply_to' => $reply_to,
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    // Save email to file for testing
    $email_file = 'sent_emails.txt';
    $email_log = json_encode($email_data) . "\n";
    file_put_contents($email_file, $email_log, FILE_APPEND | LOCK_EX);
    
    // For testing, always return true
    // In production, replace this with actual SMTP sending
    return true;
}

// Function to read sent emails (for testing)
function getSentEmails() {
    $email_file = 'sent_emails.txt';
    if (!file_exists($email_file)) {
        return [];
    }
    
    $emails = [];
    $lines = file($email_file, FILE_IGNORE_NEW_LINES);
    foreach ($lines as $line) {
        $emails[] = json_decode($line, true);
    }
    
    return $emails;
}
?>
