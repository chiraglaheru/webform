<?php
// Start session if needed (for showing success messages)
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Submitted Successfully</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .success-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            text-align: center;
        }
        h1 {
            color: #2ecc71;
        }
        .icon {
            font-size: 60px;
            color: #2ecc71;
            margin-bottom: 20px;
        }
        .details {
            text-align: left;
            margin-top: 30px;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 5px;
        }
        .action-buttons {
            margin-top: 30px;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin: 0 10px;
            background: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s;
        }
        .btn:hover {
            background: #2980b9;
        }
        .btn-print {
            background: #95a5a6;
        }
        .btn-print:hover {
            background: #7f8c8d;
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="icon">✓</div>
        <h1>Application Submitted Successfully!</h1>
        <p>Thank you for submitting your application. We have received all your details.</p>
        
        <div class="details">
            <h3>What Happens Next?</h3>
            <ul>
                <li>Your application will be reviewed by our committee</li>
                <li>You will receive a confirmation email shortly</li>
                <li>We may contact you for additional information if needed</li>
                <li>The selection process typically takes 2-3 weeks</li>
            </ul>
            
            <h3>Application Reference</h3>
            <p>Your application reference number is: <strong><?php echo isset($_SESSION['application_id']) ? $_SESSION['application_id'] : 'N/A'; ?></strong></p>
            <p>Please keep this number for future reference.</p>
        </div>
        
        <div class="action-buttons">
            <a href="javascript:window.print()" class="btn btn-print">Print Confirmation</a>
            <a href="index.php" class="btn">Return to Home</a>
        </div>
    </div>
</body>
</html>