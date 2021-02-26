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
     * Liste des noms de qualification des services fournis.
     * @var string[]
     */
    protected $provides = [
        MailerInterface::class,
        MailerDriverInterface::class,
        MailableInterface::class,
    ];

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->getContainer()->share(
            MailerInterface::class,
            function () {
                return new Mailer([], $this->getContainer());
            }
        );

        $this->getContainer()->add(
            MailerDriverInterface::class,
            function () {
                return new PhpMailerDriver(new PHPMailer(Env::isDev()));
            }
        );

        $this->getContainer()->share(
            MailableInterface::class,
            function () {
                return new Mailable($this->getContainer()->get(MailerInterface::class));
            }
        );

        $this->registerViewEngine();
    }

    /**
     * Déclaration du moteur d'affichage.
     *
     * @return void
     */
    public function registerViewEngine(): void
    {
        $this->getContainer()->add(
            MailableViewEngineInterface::class,
            function () {
                return new MailableViewEngine();
            }
        );
    }
}