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

```php
use Pollen\Mail\Mailer;

$mailer = new Mailer(
    [
        'to'   => ['hello@example.com', 'Hello Example'],
        'from' => ['contact@example.com', 'Contact Example'],
    ]
);

$mailer->debug();
```