<?php
return [
    # The SMTP server host to use
    'smtp_host' => 'smtp.gmail.com',
    # The SMTP username to use
    'smtp_username' => '${CONTACT_FORM_SMTP_USERNAME}',
    # The SMTP password to use
    'smtp_password' => '${CONTACT_FORM_SMTP_PASSWORD}',
    'smtp_secure' => 'tls',
    'smtp_port' => 587,
    # The email address to which will send emails
    'to_email' => '${CONTACT_FORM_TO_EMAIL}',
    # Will be included in the email subject
    'site_title' => 'IvanDachev.COM',
    # A list of trusted origins, remove the localhost when deploying
    'trusted_origins' => ['https://ivandachev.com', 'http://localhost:4000'],
    # The regular expression to validate the email address
    'email_regexp' => '/^(([^<>()[\]\.,;:\s@\"]+(\.[^<>()[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i',
    # The min and max length of the name field
    'min_name_length' => 2,
    'max_name_length' => 100,
    # The min and max length of the message field
    'min_message_length' => 50,
    'max_message_length' => 8000,
];
?>