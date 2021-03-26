<?php

declare(strict_types=1);

namespace Pollen\Mail;

use Pollen\View\PartialAwareViewLoader;
use Pollen\View\ViewLoader;
use RuntimeException;

class MailableViewLoader extends ViewLoader implements MailableViewLoaderInterface
{
    use PartialAwareViewLoader;

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

        throw new RuntimeException('MailableViewLoader must have a delegate Mailable instance');
    }

    /**
     * Récupération de l'instance du moteur d'expédition du mail.
     *
     * @return MailerDriverInterface
     */
    protected function getMailer(): MailerDriverInterface
    {
        return $this->getDelegate()->getMailer();
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

    /**
     * @inheritDoc
     */
    public function getAttachments(): array
    {
        return $this->getMailer()->getAttachments();
    }

    /**
     * @inheritDoc
     */
    public function getBcc(): array
    {
        return $this->getMailer()->getBcc();
    }

    /**
     * @inheritDoc
     */
    public function getCc(): array
    {
        return $this->getMailer()->getCc();
    }

    /**
     * @inheritDoc
     */
    public function getCharset(): string
    {
        return $this->getMailer()->getCharset();
    }

    /**
     * @inheritDoc
     */
    public function getContentType(): string
    {
        return $this->getMailer()->getContentType();
    }

    /**
     * @inheritDoc
     */
    public function getEncoding(): string
    {
        return $this->getMailer()->getEncoding();
    }

    /**
     * @inheritDoc
     */
    public function getFrom(): array
    {
        return $this->getMailer()->getFrom();
    }

    /**
     * @inheritDoc
     */
    public function getHeaders(): array
    {
        return $this->getMailer()->getHeaders();
    }

    /**
     * @inheritDoc
     */
    public function getHtml(): string
    {
        return $this->getMailer()->getHtml();
    }

    /**
     * @inheritDoc
     */
    public function getLocale(): string
    {
        return $this->getDelegate()->getLocale();
    }

    /**
     * @inheritDoc
     */
    public function getMessage(): string
    {
        return $this->getMailer()->getMessage();
    }

    /**
     * @inheritDoc
     */
    public function getReplyTo(): array
    {
        return $this->getMailer()->getReplyTo();
    }

    /**
     * @inheritDoc
     */
    public function getSubject(): string
    {
        return $this->getMailer()->getSubject();
    }

    /**
     * @inheritDoc
     */
    public function getText(): string
    {
        return $this->getMailer()->getText();
    }

    /**
     * @inheritDoc
     */
    public function getTo(): array
    {
        return $this->getMailer()->getTo();
    }

    /**
     * @inheritDoc
     */
    public function hasHtml(): bool
    {
        return $this->getMailer()->hasHtml();
    }

    /**
     * @inheritDoc
     */
    public function hasText(): bool
    {
        return $this->getMailer()->hasText();
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
}