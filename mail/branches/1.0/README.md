# Pollen Mail Component

[![Latest Version](https://img.shields.io/badge/release-1.0.0-blue?style=for-the-badge)](https://www.presstify.com/pollen-solutions/mail/)
[![MIT Licensed](https://img.shields.io/badge/license-MIT-green?style=for-the-badge)](LICENSE.md)
[![PHP Supported Versions](https://img.shields.io/badge/PHP->=7.4-8892BF?style=for-the-badge&logo=php)](https://www.php.net/supported-versions.php)

Pollen **Mail** Component provides an api to create and send email. 

## Installation

```bash
composer require pollen-solutions/mail
```

## Basic Usage

### Send

```php
use Pollen\Mail\MailManager;

$mail = new MailManager(
    [
        'to'   => ['hello@example.com', 'Hello Example'],
        'from' => ['contact@example.com', 'Contact Example'],
    ]
);

$mail->send();
```

### Queue (coming soon)

[...]

### Debug

```php
use Pollen\Mail\MailManager;

$mail = new MailManager(
    [
        'to'   => ['hello@example.com', 'Hello Example'],
        'from' => ['contact@example.com', 'Contact Example'],
    ]
);

$mail->debug();
```

### Through a Mailable Object

#### Default Mailable instance

```php
use Pollen\Mail\MailManager;
use Pollen\Mail\Mailable;

$mail = new MailManager();
$mailable = new Mailable($mail);
$mailable
    ->setFrom(['contact@example.com', 'Contact Example'])
    ->setTo(['hello@example.com', 'Hello Example']);

$mailable->send();
```

#### Own Mailable instance

```php
use Pollen\Mail\MailManager;
use Pollen\Mail\Mailable;

$mail = new MailManager();
$mailable = new Mailable($mail);
$mailable
    ->setFrom(['contact@example.com', 'Contact Example'])
    ->setTo(['hello@example.com', 'Hello Example']);

$mailable->send();
```


## Test Usage

### Configure Transport with MailHog

MailHog must be installed and running on your server.
More details : https://github.com/mailhog/MailHog

```php
use Pollen\Mail\MailManager;
use Pollen\Mail\Drivers\PhpMailerDriver;

$mail = new MailManager();

$mail->setTransportConfigCallback(function (PhpMailerDriver $mailer) {
    $mailer->isSMTP();
    $mailer->Host = '0.0.0.0';
    $mailer->Username = 'mailhog.example';
    $mailer->Port = 1025;
});

$mail->send();

## Visit http://0.0.0.0:8025/
```