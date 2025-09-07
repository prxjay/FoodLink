<?php
// Email testing page - shows what emails would be sent
include 'simple_smtp.php';

$sent_emails = getSentEmails();
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
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        h1 {
            color: #8aaee0;
            text-align: center;
            margin-bottom: 30px;
        }
        .email-item {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            background: #f8f9fa;
        }
        .email-header {
            background: #8aaee0;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        .email-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 15px;
        }
        .email-details p {
            margin: 5px 0;
        }
        .email-details strong {
            color: #8aaee0;
        }
        .email-message {
            background: white;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #8aaee0;
            max-height: 300px;
            overflow-y: auto;
        }
        .no-emails {
            text-align: center;
            color: #666;
            font-style: italic;
            padding: 40px;
        }
        .back-btn {
            display: inline-block;
            background: #8aaee0;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .back-btn:hover {
            background: #6b9bd2;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="unified_dashboard.php" class="back-btn">← Back to Dashboard</a>
        
        <h1>📧 Email Test Results</h1>
        
        <?php if (empty($sent_emails)): ?>
            <div class="no-emails">
                <h3>No emails sent yet</h3>
                <p>Try claiming a food item to see the email notification here.</p>
            </div>
        <?php else: ?>
            <p><strong>Total emails: <?php echo count($sent_emails); ?></strong></p>
            
            <?php foreach (array_reverse($sent_emails) as $index => $email): ?>
                <div class="email-item">
                    <div class="email-header">
                        <h3>Email #<?php echo count($sent_emails) - $index; ?> - <?php echo htmlspecialchars($email['subject']); ?></h3>
                    </div>
                    
                    <div class="email-details">
                        <div>
                            <p><strong>To:</strong> <?php echo htmlspecialchars($email['to']); ?></p>
                            <p><strong>From:</strong> <?php echo htmlspecialchars($email['from_name']); ?> &lt;<?php echo htmlspecialchars($email['from']); ?>&gt;</p>
                            <p><strong>Reply-To:</strong> <?php echo htmlspecialchars($email['reply_to']); ?></p>
                        </div>
                        <div>
                            <p><strong>Subject:</strong> <?php echo htmlspecialchars($email['subject']); ?></p>
                            <p><strong>Sent:</strong> <?php echo htmlspecialchars($email['timestamp']); ?></p>
                            <p><strong>Status:</strong> <span style="color: #28a745;">✅ Sent Successfully</span></p>
                        </div>
                    </div>
                    
                    <div class="email-message">
                        <h4>Email Content:</h4>
                        <?php echo $email['message']; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>
