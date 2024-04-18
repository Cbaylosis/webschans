<?php
/**
 * Requires the "PHP Email Form" library
 * The "PHP Email Form" library is available only in the pro version of the template
 * The library should be uploaded to: vendor/php-email-form/php-email-form.php
 * For more info and help: https://bootstrapmade.com/php-email-form/
 */

// Path to the php-email-form.php file
$email_form_path = '../assets/vendor/php-email-form/php-email-form.php';

// Check if the file exists
if (file_exists($email_form_path)) {
  require $email_form_path;
} else {
  die('Unable to load the "PHP Email Form" Library!');
}

// Replace contact@example.com with your real receiving email address
$receiving_email_address = 'christiancbaylosis@gmail.com';

$contact = new PHP_Email_Form;
$contact->ajax = true;

$contact->to = $receiving_email_address;
$contact->from_name = $_POST['name'];
$contact->from_email = $_POST['email'];
$contact->subject = $_POST['subject'];

// Uncomment below code if you want to use SMTP to send emails. You need to enter your correct SMTP credentials

$contact->smtp = array(
  'host' => 'smtp.gmail.com',
  'username' => 'nap.cbaylosis@gmail.com',
  'password' => 'pass',
  'port' => '587'
);


$contact->add_message($_POST['name'], 'From');
$contact->add_message($_POST['email'], 'Email');
$contact->add_message($_POST['message'], 'Message', 10);

echo $contact->send();
?>