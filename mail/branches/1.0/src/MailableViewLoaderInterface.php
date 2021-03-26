<?php

declare(strict_types=1);

namespace Pollen\Mail;

use Pollen\View\PartialAwareViewLoaderInterface;
use Pollen\View\ViewLoaderInterface;

interface MailableViewLoaderInterface extends PartialAwareViewLoaderInterface, ViewLoaderInterface
{
    /**
     * Récupération de la liste des pièces jointes.
     *
     * @return array
     */
    public function getAttachments(): array;

    /**
     * Récupération de la liste des destinataires en copie cachée.
     *
     * @return array
     */
    public function getBcc(): array;

    /**
     * Récupération de la liste des destinataires en copie carbone.
     *
     * @return array
     */
    public function getCc(): array;

    /**
     * Récupération de l'encodage des caractères.
     *
     * @return string
     */
    public function getCharset(): string;

    /**
     * Récupération du type de contenu du message.
     *
     * @return string
     */
    public function getContentType(): string;

    /**
     * Récupération de l'encodage du message.
     *
     * @return string
     */
    public function getEncoding(): string;

    /**
     * Récupération de l'expéditeur du message.
     *
     * @return array
     */
    public function getFrom(): array;

    /**
     * Récupération de la liste des entêtes du messages.
     *
     * @return array
     */
    public function getHeaders(): array;

    /**
     * Récupération du message au format HTML.
     *
     * @return string
     */
    public function getHtml(): string;

    /**
     * Récupération de la langue d'expédition du message.
     *
     * @return string
     */
    public function getLocale(): string;

    /**
     * Récupération du message au format texte brut ou HTML.
     *
     * @return string
     */
    public function getMessage(): string;

    /**
     * Récupération de la liste des destinataires en réponse au message.
     *
     * @return array
     */
    public function getReplyTo(): array;

    /**
     * Récupération de l'objet du message.
     *
     * @return string
     */
    public function getSubject(): string;

    /**
     * Récupération du message au format texte brut.
     *
     * @return string
     */
    public function getText(): string;

    /**
     * Récupération de la liste des destinataires.
     *
     * @return array
     */
    public function getTo(): array;

    /**
     * Vérification de la présence du format HTML dans le message.
     *
     * @return bool
     */
    public function hasHtml(): bool;

    /**
     * Vérification de la présence du format texte brut dans le message.
     *
     * @return bool
     */
    public function hasText(): bool;

    /**
     * Linéarisation des informations de contact.
     *
     * @param array $contacts Informations de contact
     *
     * @return array
     */
    public function linearizeContacts(array $contacts): array;
}