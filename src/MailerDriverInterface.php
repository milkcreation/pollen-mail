<?php

declare(strict_types=1);

namespace Pollen\Mail;

interface MailerDriverInterface
{
    /**
     * Ajout d'une pièce jointe.
     *
     * @param string $path Chemin absolu vers le fichier.
     *
     * @return static
     */
    public function addAttachment(string $path): MailerDriverInterface;

    /**
     * Ajout d'un destinataire copie cachée.
     *
     * @param string $email
     * @param string $name
     *
     * @return static
     */
    public function addBcc(string $email, string $name = ''): MailerDriverInterface;

    /**
     * Ajout d'un destinataire copie carbone.
     *
     * @param string $email
     * @param string $name
     *
     * @return static
     */
    public function addCc(string $email, string $name = ''): MailerDriverInterface;

    /**
     * Ajout d'un destinataire de réponse.
     *
     * @param string $email
     * @param string $name
     *
     * @return static
     */
    public function addReplyTo(string $email, string $name = ''): MailerDriverInterface;

    /**
     * Ajout d'un destinataire.
     *
     * @param string $email
     * @param string $name
     *
     * @return static
     */
    public function addTo(string $email, string $name = ''): MailerDriverInterface;

    /**
     * Message d'erreur de traitement.
     *
     * @return string
     */
    public function error(): string;

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
     * Récupération de l'encodage du message.
     *
     * @return string
     */
    public function getCharset(): string;

    /**
     * Récupération du type de contenu.
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
     * Récupération de la liste des entêtes.
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
     * Récupération du message au format Texte brut ou HTML.
     *
     * @return string
     */
    public function getMessage(): string;

    /**
     * Récupération de la liste des destinataires en réponse.
     *
     * @return array
     */
    public function getReplyTo(): array;

    /**
     * Récupération du sujet du message.
     *
     * @return string
     */
    public function getSubject(): string;

    /**
     * Récupération du message au format texte.
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
     * Préparation de l'email en vue de l'expédition.
     *
     * @return boolean
     */
    public function prepare(): bool;

    /**
     * Expédition de l'email.
     *
     * @return boolean
     */
    public function send(): bool;

    /**
     * Définition de l'encodage des caractères.
     *
     * @param string $charset
     *
     * @return static
     */
    public function setCharset(string $charset = 'utf-8'): MailerDriverInterface;

    /**
     * Définition du type de contenu.
     *
     * @param string $content_type multipart/alternative|text/html|text/plain
     *
     * @return static
     */
    public function setContentType(string $content_type = 'multipart/alternative'): MailerDriverInterface;

    /**
     * Définition de l'encodage du message.
     *
     * @param string $encoding 8bit|7bit|binary|base64|quoted-printable.
     *
     * @return static
     */
    public function setEncoding(string $encoding): MailerDriverInterface;

    /**
     * Définition de l'expéditeur.
     *
     * @param string $email
     * @param string $name
     *
     * @return static
     */
    public function setFrom(string $email, string $name = ''): MailerDriverInterface;

    /**
     * Définition du message au format HTML.
     *
     * @param string $message
     *
     * @return static
     */
    public function setHtml(string $message): MailerDriverInterface;

    /**
     * Définition du sujet.
     *
     * @param string $subject
     *
     * @return static
     */
    public function setSubject(string $subject = ''): MailerDriverInterface;

    /**
     * Définition du message au format texte.
     *
     * @param string $text
     *
     * @return static
     */
    public function setText(string $text): MailerDriverInterface;
}