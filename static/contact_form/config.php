<?php
return [
    'smtp_host' => 'smtp.gmail.com',
    'smtp_username' => 'your-email@gmail.com',
    'smtp_password' => 'your-app-password',
    'smtp_secure' => 'tls',
    'smtp_port' => 587,
    'to_email' => 'to_email@test.com', # email address to which will send emails
    'redirect_url' => 'https://test/contact-res', # redirect to this on success or failure
    'site_title' => 'My Site Title', # will be included in the email subject
];
?>