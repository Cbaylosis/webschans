<?php

class PHP_Email_Form
{
    public $ajax = false;
    public $to = '';
    public $from_name = '';
    public $from_email = '';
    public $subject = '';
    public $message = '';

    public function add_message($message, $label, $maxlength)
    {
        // Truncate message if it exceeds the maximum length
        if (strlen($message) > $maxlength) {
            $message = substr($message, 0, $maxlength);
        }

        // Add message content to the email body
        $this->message .= "$label: $message\n";
    }


    public function send()
    {
        // Send the email using the configured settings
        $headers = "From: $this->from_name <$this->from_email>\r\n";
        $headers .= "Reply-To: $this->from_email\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

        // Use PHP's mail() function to send the email
        return mail($this->to, $this->subject, $this->message, $headers);
    }
}
