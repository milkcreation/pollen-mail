<?php

declare(strict_types=1);

namespace Pollen\Mail;

use Pollen\Container\BaseServiceProvider;
use Pollen\Mail\Drivers\PhpMailerDriver;
use PHPMailer\PHPMailer\PHPMailer;
use Pollen\Support\Env;

class MailServiceProvider extends BaseServiceProvider
{
    /**
     * @var string[]
     */
    protected $provides = [
        MailManagerInterface::class,
        MailableInterface::class,
        MailerDriverInterface::class,
        MailQueueFactoryInterface::class
    ];

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->getContainer()->share(
            MailManagerInterface::class,
            function () {
                return new MailManager([], $this->getContainer());
            }
        );

        $this->getContainer()->add(
            MailerDriverInterface::class,
            function () {
                return new PhpMailerDriver(new PHPMailer(Env::isDev()));
            }
        );

        $this->getContainer()->add(
            MailableInterface::class,
            function () {
                return new Mailable($this->getContainer()->get(MailManagerInterface::class));
            }
        );

        $this->getContainer()->share(
            MailQueueFactoryInterface::class,
            function () {
                return new MailQueueFactory($this->getContainer()->get(MailManagerInterface::class));
            }
        );
    }
}