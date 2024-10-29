<?php
require 'vendor/autoload.php';

use Google\Client;
use Google\Service\Gmail;

// Create a new Google client
$client = new Client();
$client->setApplicationName('Gmail API PHP Quickstart');
$client->setScopes(Gmail::GMAIL_SEND);
$client->setAuthConfig(__DIR__ . '/credentials.json');
$client->setAccessType('offline');
$client->setRedirectUri('http://localhost:8080');

// Check if the token.json file exists
if (file_exists('token.json')) {
    $accessToken = json_decode(file_get_contents('token.json'), true);
    $client->setAccessToken($accessToken);
}

// Refresh token if expired
if ($client->isAccessTokenExpired()) {
    if ($client->getRefreshToken()) {
        $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
    } else {
        // Request authorization from the user
        $authUrl = $client->createAuthUrl();
        echo "Open the following link in your browser:<br><a href='$authUrl' target='_blank'>$authUrl</a><br>";
        echo '<form method="POST" action="">';
        echo 'Enter verification code: <input type="text" name="code" required>';
        echo '<input type="submit" value="Submit">';
        echo '</form>';
        exit; // Exit after showing the form
    }
}

// Use the Gmail service
$service = new Gmail($client);

// Check if the script is being accessed via a web server
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['code'])) {
        // Fetch the access token using the provided verification code
        $accessToken = $client->fetchAccessTokenWithAuthCode(trim($_POST['code']));
        $client->setAccessToken($accessToken);
        file_put_contents('token.json', json_encode($client->getAccessToken()));

        echo 'Authorization successful! You can now send emails.<br>';
    }

    // Get and sanitize the form data
    $to = "nap.cbaylosis@gmail.com"; // Change to the recipient's email address
    $name = isset($_POST['name']) ? htmlspecialchars(trim($_POST['name'])) : 'No Name';
    $userEmail = isset($_POST['email']) ? htmlspecialchars(trim($_POST['email'])) : 'no-reply@example.com';
    $subject = isset($_POST['subject']) ? htmlspecialchars(trim($_POST['subject'])) : 'No Subject';
    $messageText = isset($_POST['message']) ? htmlspecialchars(trim($_POST['message'])) : 'No Message';

    // Create the email body
    $email = "From: $userEmail\r\n";
    $email .= "Reply-To: $userEmail\r\n";
    $email .= "To: $to\r\n";
    $email .= "Subject: $subject\r\n\r\n";
    $email .= "Message from $name:\n\n$messageText";

    // Encode the email
    $rawMessage = base64_encode($email);
    $rawMessage = str_replace(array('+', '/', '='), array('-', '_', ''), $rawMessage);

    $message = new Google\Service\Gmail\Message();
    $message->setRaw($rawMessage);

    try {
        // Send the email
        $service->users_messages->send('me', $message);
        echo 'Email sent successfully!';
    } catch (Exception $e) {
        // Better error handling
        echo 'An error occurred while sending the email: ' . htmlspecialchars($e->getMessage());
    }
} else {
    echo "This script can only be run via a web server.";
}
?>