<?php

declare(strict_types=1);

namespace Pollen\Mail;

use Pollen\View\ViewTemplate;
use RuntimeException;

class MailableViewTemplate extends ViewTemplate implements MailableViewTemplateInterface
{
    /**
     * @inheritDoc
     */
    public function driver(): ?MailerDriverInterface
    {
        return $this->getDelegate()->mailer()->getDriver();
    }

    /**
     * @inheritDoc
     */
    public function param(string $key, $default = null)
    {
        return $this->getDelegate()->params($key, $default);
    }

    /**
     * @inheritDoc
     */
    public function linearizeContacts(array $contacts): array
    {
        foreach($contacts as &$contact) {
            $contact = $this->linearizeContact(...$contact);
        }
        return $contacts;
    }

    /**
     * Récupération de l'instance de délégation.
     *
     * @return MailableInterface
     */
    protected function getDelegate(): MailableInterface
    {
        /** @var MailableInterface|object|null $delegate */
        $delegate = $this->engine->getDelegate();
        if ($delegate instanceof MailableInterface) {
            return $delegate;
        }

        throw new RuntimeException('MailableViewTemplate must have a delegate Mailable instance');
    }

    /**
     * Linéarisation de données d'un contact.
     *
     * @param string $email
     * @param string|null $name
     *
     * @return string
     */
    protected function linearizeContact(string $email, ?string $name = null): string
    {
        return $name !== null ? "{$name} <{$email}>" : $email;
    }
}