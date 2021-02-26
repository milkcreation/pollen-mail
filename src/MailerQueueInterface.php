<?php

declare(strict_types=1);

namespace Pollen\Mail;

use DateTimeInterface;
use Pollen\Support\Proxy\MailerProxyInterface;

interface MailerQueueInterface extends MailerProxyInterface
{
    /**
     * Ajout d'un élément dans la file d'attente
     *
     * @param MailableInterface $mail
     * @param DateTimeInterface|string $date.
     * @param array $context
     *
     * @return int
     */
    public function add(MailableInterface $mail, $date = 'now', array $context = []): int;
}