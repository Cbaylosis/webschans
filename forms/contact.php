<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name = $_POST['name'];
  $email = $_POST['email'];
  $subject = $_POST['subject'];
  $message = $_POST['message'];

  // Instantiate PHPMailer
  $mail = new PHPMailer(true);

  try {
    //Server settings
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'nap.cbaylosis@gmail.com';
    $mail->Password = 'NAPbaylosis2024';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    //Recipients
    $mail->setFrom('nap.cbaylosis@gmail.com', 'Christian');
    $mail->addAddress('christiancbaylosis@gmail.com');

    // Content
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = "Name: $name<br>Email: $email<br>Message: $message";

    $mail->send();
    echo 'Message has been sent';
  } catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
  }
}