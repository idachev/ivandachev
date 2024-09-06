<?php
return [
    'smtp_host' => 'smtp.gmail.com',
    'smtp_username' => '${CONTACT_FORM_SMTP_USERNAME}',
    'smtp_password' => '${CONTACT_FORM_SMTP_PASSWORD}',
    'smtp_secure' => 'tls',
    'smtp_port' => 587,
    'to_email' => '${CONTACT_FORM_TO_EMAIL}', # email address to which will send emails
    'redirect_url_prefix' => 'https://ivandachev.com/', # prefix to check for redirect on success or failure
    'site_title' => 'IvanDachev.COM', # will be included in the email subject
    'trusted_domains' => ['ivandachev.com', 'localhost'], # list of trusted domains
];
?>