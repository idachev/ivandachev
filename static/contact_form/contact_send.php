<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './phpmailer/PHPMailer.php';
require './phpmailer/Exception.php';
require './phpmailer/SMTP.php';

const HTTP_STATUS_OK = 200;
const HTTP_STATUS_BAD_REQUEST = 400;

const HEADER_CONTENT_TYPE = 'Content-Type';
const CONTENT_TYPE_APPLICATION_JSON = 'application/json';

$config = include('./config.php');

$email_regexp = $config['email_regexp'] ?? '/^(([^<>()[\]\.,;:\s@\"]+(\.[^<>()[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i';

$min_name_length = $config['min_name_length'] ?? 2;
$max_name_length = $config['max_name_length'] ?? 100;

$min_message_length = $config['min_message_length'] ?? 50;
$max_message_length = $config['max_message_length'] ?? 8000;

$allowed_origins = $config['allowed_origins'] ?? ['http://localhost:4000'];

$http_origin = $_SERVER['HTTP_ORIGIN'] ?? '';

$allowed_origin = get_allowed_origin($http_origin, $allowed_origins);

if ($allowed_origin !== null) {
    header('Access-Control-Allow-Origin: ' . $allowed_origin);
} else {
    header('Access-Control-Allow-Origin: ' . $allowed_origins[0]);
}

header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

function get_allowed_origin($http_origin_check, $allowed_origins)
{
    if (empty($http_origin_check)) {
        return null;
    }

    foreach ($allowed_origins as $allowed_origin) {
        if (strpos($http_origin_check, $allowed_origin) === 0) {
            return $allowed_origin;
        }
    }

    return null;
}

function return_response($error_message = '')
{
    if (empty($error_message)) {
        http_response_code(HTTP_STATUS_OK);
    } else {
        http_response_code(HTTP_STATUS_BAD_REQUEST);
    }

    $response = [
        'error' => $error_message,
        'timestamp' => gmdate('c')
    ];

    $response_json = json_encode($response);

    header(HEADER_CONTENT_TYPE . ': ' . CONTENT_TYPE_APPLICATION_JSON);

    echo $response_json;

    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (get_allowed_origin($http_origin, $allowed_origins) === null) {
        return_response("Not allowed origin: " . $http_origin);
    }

    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $message = htmlspecialchars(trim($_POST['message']));

    if (!preg_match($email_regexp, $email)) {
        return_response("Invalid email address.");
    }

    if (strlen($name) < $min_name_length || strlen($name) > $max_name_length) {
        return_response("Name must be between $min_name_length and $max_name_length characters.");
    }

    if (strlen($message) < $min_message_length || strlen($message) > $max_message_length) {
        return_response("Message must be between $min_message_length and $max_message_length characters.");
    }

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
            return_response();
        } else {
            return_response("Mailer Error: " . $mail->ErrorInfo);
        }
    } catch (Exception $e) {
        return_response("Mailer Exception: " . $e->getMessage());
    }
} else if ($_SERVER["REQUEST_METHOD"] != "OPTIONS") {
    return_response("Invalid request.");
}
?>
