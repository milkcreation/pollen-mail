<?php

declare(strict_types=1);

namespace Pollen\Mail;

use DateTimeInterface;
use Pollen\Http\ResponseInterface;
use Pollen\Support\Concerns\ParamsBagAwareTraitInterface;
use Pollen\Support\ParamsBag;
use Pollen\Support\Proxy\MailProxyInterface;
use Pollen\Support\Proxy\PartialProxyInterface;
use Pollen\View\ViewEngineInterface;
use InvalidArgumentException;

interface MailableInterface extends MailProxyInterface, ParamsBagAwareTraitInterface, PartialProxyInterface
{
    /**
     * Résolution de sortie de la classe sous la forme d'une chaîne de caractères.
     *
     * @return string
     */
    public function __toString(): string;

    /**
     * Préparation du mail pour l'expédition.
     *
     * @return static
     */
    public function build(): MailableInterface;

    /**
     * Définition|Récupération|Instance des données associées aux gabarits de message.
     *
     * @param array|string|null $key
     * @param mixed $default
     *
     * @return string|int|array|mixed|ParamsBag
     *
     * @throws InvalidArgumentException
     */
    public function datas($key = null, $default = null);

    /**
     * Récupération de la langue d'expédition du message.
     *
     * @return string
     */
    public function getLocale(): string;

    /**
     * Récupération du moteur d'expédition des mails.
     *
     * @return MailerDriverInterface
     */
    public function getMailer(): MailerDriverInterface;

    /**
     * Récupération de l'affichage du mode de débogage.
     *
     * @return string
     */
    public function debug(): string;

    /**
     * Mise en file de l'email dans la queue d'expédition.
     *
     * @param DateTimeInterface|string $date Date d'expédition.
     * @param array $params Liste des paramètres complémentaires.
     *
     * @return int
     */
    public function queue($date = 'now', array $params = []): int;

    /**
     * Rendu d'affichage du message au format Texte ou HTML.
     *
     * @return string
     */
    public function message(): string;

    /**
     * Récupération de la réponse HTTP.
     *
     * @return ResponseInterface
     */
    public function response(): ResponseInterface;

    /**
     * Expédition de l'email
     *
     * @return bool
     */
    public function send(): bool;

    /**
     * Définition de la liste des pièces jointes au message.
     *
     * @param string|array $attachments
     *
     * @return static
     */
    public function setAttachments($attachments): MailableInterface;

    /**
     * Définition des destinataires en copie cachée.
     *
     * @param string|array $bcc
     *
     * @return static
     */
    public function setBcc($bcc): MailableInterface;

    /**
     * Définition des destinataires en copie carbone.
     *
     * @param string|array $cc
     *
     * @return static
     */
    public function setCc($cc): MailableInterface;

    /**
     * Définition des propriétés CSS du message HTML.
     *
     * @param string $css
     *
     * @return static
     */
    public function setCss(string $css): MailableInterface;

    /**
     * Définition du jeu de caractères.
     *
     * @param string $charset
     *
     * @return static
     */
    public function setCharset(string $charset): MailableInterface;

    /**
     * Définition du type de contenu.
     *
     * @param string $contentType
     *
     * @return static
     */
    public function setContentType(string $contentType): MailableInterface;

    /**
     * Définition de l'encodage.
     *
     * @param string $encoding
     *
     * @return static
     */
    public function setEncoding(string $encoding): MailableInterface;

    /**
     * Définition de l'expéditeur du message.
     *
     * @param string|array $from
     *
     * @return static
     */
    public function setFrom($from): MailableInterface;

    /**
     * Définition du contenu HTML du message.
     *
     * @param string $html
     *
     * @return static
     */
    public function setHtml(string $html): MailableInterface;

    /**
     * Définition du formatage des propriétés CSS dans les balises HTML.
     *
     * @param bool $inlineCss
     *
     * @return static
     */
    public function setInlineCss(bool $inlineCss = true): MailableInterface;

    /**
     * Définition de la langue du message.
     *
     * @param string $locale
     *
     * @return static
     */
    public function setLocale(string $locale): MailableInterface;

    /**
     * Définition du moteur d'expédition des mails
     *
     * @param MailerDriverInterface $mailer
     *
     * @return static
     */
    public function setMailer(MailerDriverInterface $mailer): MailableInterface;

    /**
     * Définition des destinataires de la réponse au message.
     *
     * @param string|array $replyTo
     *
     * @return static
     */
    public function setReplyTo($replyTo): MailableInterface;

    /**
     * Définition de l'objet du message.
     *
     * @param string $subject
     *
     * @return static
     */
    public function setSubject(string $subject): MailableInterface;

    /**
     * Définition du texte brut du message.
     *
     * @param string $text
     *
     * @return static
     */
    public function setText(string $text): MailableInterface;

    /**
     * Définition des destinataires du message.
     *
     * @param string|array $to
     *
     * @return static
     */
    public function setTo($to): MailableInterface;

    /**
     * Récupération de l'affichage d'un gabarit.
     *
     * @param string|null $view
     * @param array $data
     *
     * @return ViewEngineInterface|string
     */
    public function view(?string $view = null, array $data = []);
}