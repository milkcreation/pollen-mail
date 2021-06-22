<?php

declare(strict_types=1);

namespace Pollen\Mail;

use Pollen\View\Engines\Plates\PlatesPartialAwareTemplateTrait;
use Pollen\View\Engines\Plates\PlatesTemplate;
use RuntimeException;

class MailableTemplate extends PlatesTemplate
{
    use PlatesPartialAwareTemplateTrait;

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

        throw new RuntimeException('MailableTemplate must have a delegate Mailable instance');
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
     * Récupération de la liste des pièces jointes.
     *
     * @return array
     */
    public function getAttachments(): array
    {
        return $this->getMailer()->getAttachments();
    }

    /**
     * Récupération de la liste des destinataires en copie cachée.
     *
     * @return array
     */
    public function getBcc(): array
    {
        return $this->getMailer()->getBcc();
    }

    /**
     * Récupération de la liste des destinataires en copie carbone.
     *
     * @return array
     */
    public function getCc(): array
    {
        return $this->getMailer()->getCc();
    }

    /**
     * Récupération de l'encodage des caractères.
     *
     * @return string
     */
    public function getCharset(): string
    {
        return $this->getMailer()->getCharset();
    }

    /**
     * Récupération du type de contenu du message.
     *
     * @return string
     */
    public function getContentType(): string
    {
        return $this->getMailer()->getContentType();
    }

    /**
     * Récupération de l'encodage du message.
     *
     * @return string
     */
    public function getEncoding(): string
    {
        return $this->getMailer()->getEncoding();
    }

    /**
     * Récupération de l'expéditeur du message.
     *
     * @return array
     */
    public function getFrom(): array
    {
        return $this->getMailer()->getFrom();
    }

    /**
     * Récupération de la liste des entêtes du messages.
     *
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->getMailer()->getHeaders();
    }

    /**
     * Récupération du message au format HTML.
     *
     * @return string
     */
    public function getHtml(): string
    {
        return $this->getMailer()->getHtml();
    }

    /**
     * Récupération de la langue d'expédition du message.
     *
     * @return string
     */
    public function getLocale(): string
    {
        return $this->getDelegate()->getLocale();
    }

    /**
     * Récupération du message au format texte brut ou HTML.
     *
     * @return string
     */
    public function getMessage(): string
    {
        return $this->getMailer()->getMessage();
    }

    /**
     * Récupération de la liste des destinataires en réponse au message.
     *
     * @return array
     */
    public function getReplyTo(): array
    {
        return $this->getMailer()->getReplyTo();
    }

    /**
     * Récupération de l'objet du message.
     *
     * @return string
     */
    public function getSubject(): string
    {
        return $this->getMailer()->getSubject();
    }

    /**
     * Récupération du message au format texte brut.
     *
     * @return string
     */
    public function getText(): string
    {
        return $this->getMailer()->getText();
    }

    /**
     * Récupération de la liste des destinataires.
     *
     * @return array
     */
    public function getTo(): array
    {
        return $this->getMailer()->getTo();
    }

    /**
     * Vérification de la présence du format HTML dans le message.
     *
     * @return bool
     */
    public function hasHtml(): bool
    {
        return $this->getMailer()->hasHtml();
    }

    /**
     * Vérification de la présence du format texte brut dans le message.
     *
     * @return bool
     */
    public function hasText(): bool
    {
        return $this->getMailer()->hasText();
    }

    /**
     * Linéarisation des informations de contact.
     *
     * @param array $contacts Informations de contact
     *
     * @return array
     */
    public function linearizeContacts(array $contacts): array
    {
        foreach($contacts as &$contact) {
            $contact = $this->linearizeContact(...$contact);
        }
        return $contacts;
    }
}