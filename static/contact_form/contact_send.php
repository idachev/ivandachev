<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './phpmailer/PHPMailer.php';
require './phpmailer/Exception.php';
require './phpmailer/SMTP.php';

const HTTP_STATUS_BAD_REQUEST = 400;

const HEADER_CONTENT_TYPE = 'Content-Type';
const CONTENT_TYPE_APPLICATION_JSON = 'application/json';

$config = include('./config.php');

function is_trusted_domain($http_referer, $trusted_domains): bool
{
    $parsed_url = parse_url($http_referer);

    $domain = $parsed_url['host'] ?? '';

    return in_array($domain, $trusted_domains);
}

function return_error($error_message)
{
    http_response_code(HTTP_STATUS_BAD_REQUEST);

    header(HEADER_CONTENT_TYPE . ': ' . CONTENT_TYPE_APPLICATION_JSON);

    $response = [
        'error' => $error_message,
        'timestamp' => gmdate('c')
    ];

    echo json_encode($response);

    exit();
}

function redirect_to($redirect_url, $error_message = "")
{
    $separator = (strpos($redirect_url, '?') === false) ? '?' : '&';

    if (empty($error_message)) {
        header("Location: " . $redirect_url);
    } else {
        header("Location: " . $redirect_url . $separator . "error_message=" . urlencode($error_message));
    }

    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $http_referer = $_SERVER['HTTP_REFERER'] ?? '';

    if (!is_trusted_domain($http_referer, $config['trusted_domains'])) {
        return_error("Untrusted domain.");
    }

    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $message = htmlspecialchars(trim($_POST['message']));

    $redirect_url = trim($_POST['redirect_url']);

    if (parse_url($redirect_url, PHP_URL_SCHEME) === null) {
        $redirect_url = rtrim($http_referer, '/') . '/' . ltrim($redirect_url, '/');
    } else {
        if (strpos($redirect_url, $config['redirect_url_prefix']) !== 0) {
            return_error("Invalid redirect URL: " . $redirect_url);
        }
    }

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
            $mail->Subject = "$site_title - Message from $name";
            $mail->Body = "<h2>$site_title Contact Form Submission</h2>
                              <p><strong>Name:</strong> $name</p>
                              <p><strong>Email:</strong> $email</p>
                              <p><strong>Message:</strong> $message</p>";

            if ($mail->send()) {
                redirect_to($redirect_url);
            } else {
                return_error("Mailer Error: " . $mail->ErrorInfo);
            }
        } catch (Exception $e) {
            return_error("Mailer Exception: " . $e->getMessage());
        }
    } else {
        redirect_to($redirect_url, "Please fill all the fields.");
    }
} else {
    return_error("Invalid request.");
}
?>
