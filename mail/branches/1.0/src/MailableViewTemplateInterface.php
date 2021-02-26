<?php

declare(strict_types=1);

namespace Pollen\Mail;

use Pollen\View\ViewTemplateInterface;

interface MailableViewTemplateInterface extends ViewTemplateInterface
{
    /**
     * Récupération de l'instance du pilote.
     *
     * @return MailerDriverInterface|null
     */
    public function driver(): ?MailerDriverInterface;

    /**
     * Récupération d'un paramètre.
     *
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function param(string $key, $default = null);

    /**
     * Linéarisation des informations de contact.
     *
     * @param array $contacts Informations de contact
     *
     * @return array
     */
    public function linearizeContacts(array $contacts): array;
}