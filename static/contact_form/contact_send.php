<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './phpmailer/PHPMailer.php';
require './phpmailer/Exception.php';
require './phpmailer/SMTP.php';

$config = include('../../../configs/ivandachev.com-contact-form-config.php');

function is_trusted_domain($referer, $trusted_domains) {
    global $config;

    $parsed_url = parse_url($referer);
    $domain = $parsed_url['host'] ?? '';

    return in_array($domain, $$config['trusted_domains']);
}

function redirect_to($error_message="")
{
    global $config;

    $redirect_url = $config['redirect_url'];

    $separator = (strpos($redirect_url, '?') === false) ? '?' : '&';

    if (empty($error_message)) {
        header("Location: " . $redirect_url);
    } else {
        header("Location: " . $redirect_url . $separator . "error_message=" . urlencode($error_message));
    }

    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $referer = $_SERVER['HTTP_REFERER'] ?? '';

    if (!is_trusted_domain($referer, $trusted_domains)) {
        redirect_to("Untrusted domain.");
    }

    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $message = htmlspecialchars(trim($_POST['message']));

    if (!empty($name) && !empty($email) && !empty($message)) {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = $config['smtp_host'];
            $mail->SMTPAuth = true;
            $mail->Username = $config['smtp_username'];
            $mail->Password = $config['smtp_password'];
            $mail->SMTPSecure = $config['smtp_secure'];
            $mail->Port = $config['smtp_port'];

            $mail->setFrom($email, $name);
            $mail->addAddress($config['to_email']);

            $site_title = $config['site_title'];
            $mail->isHTML(true);
            $mail->Subject = "$site_title Contact Form Submission - $name";
            $mail->Body    = "<h2>$site_title Contact Form Submission</h2>
                              <p><strong>Name:</strong> $name</p>
                              <p><strong>Email:</strong> $email</p>
                              <p><strong>Message:</strong> $message</p>";

            if ($mail->send()) {
                redirect_to();
            } else {
                redirect_to("Mailer Error: " . $mail->ErrorInfo);
            }
        } catch (Exception $e) {
            redirect_to("Mailer Exception: " . $e->getMessage());
        }
    } else {
        redirect_to("Please fill all the fields.");
    }
} else {
    redirect_to("Invalid request.");
}
?>
