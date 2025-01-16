<?php
// Define the recipient email address
$to = "ismail@icpbd.com"; // Replace with the recipient's email address

// Define the subject
$subject = "ERP Software";

// Define the message
$message = "<html><body>"
    . "<h1>Hello!</h1>"
    . "<p>This is a test email sent using PHP.</p>"
    . "<p>Have a great day!</p>"
    . "</body></html>";

// Set the email headers
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
$headers .= "From: sender@example.com" . "\r\n"; // Replace with the sender's email address
$headers .= "Reply-To: sender@example.com" . "\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

// Send the email
if(mail($to, $subject, $message, $headers)) {
    echo "Email sent successfully!";
} else {
    echo "Failed to send email.";
}
?>