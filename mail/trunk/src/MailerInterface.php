<?php

declare(strict_types=1);

namespace Pollen\Mail;

use DateTimeInterface;
use Pollen\Support\Concerns\BootableTraitInterface;
use Pollen\Support\Concerns\ConfigBagAwareTraitInterface;
use Pollen\Support\Proxy\ContainerProxyInterface;

interface MailerInterface extends BootableTraitInterface, ConfigBagAwareTraitInterface, ContainerProxyInterface
{
    /**
     * Chargement.
     *
     * @return static
     */
    public function boot(): MailerInterface;

    /**
     * Définition de la liste des paramètres globaux par défaut des emails.
     *
     * @param array $attrs
     *
     * @return void
     */
    public static function setDefaults(array $attrs = []): void;

    /**
     * Création d'une instance d'email.
     *
     * @param array $params
     *
     * @return MailableInterface
     */
    public function createMailable(array $params = []): MailableInterface;

    /**
     * Affichage du message en mode débogage.
     *
     * @param MailableInterface|array|null $attrs Instance de l'email|Paramètres d'email|Email courant.
     *
     * @return void
     */
    public function debug($attrs = null): void;

    /**
     * Récupération de paramètres par défaut.
     *
     * @param string|null $key
     * @param mixed $default
     *
     * @return string|int|array|object|mixed|null
     */
    public function getDefaults(?string $key = null, $default = null);

    /**
     * Récupération du pilote de traitement des e-mails.
     *
     * @return MailerDriverInterface
     */
    public function getDriver(): MailerDriverInterface;

    /**
     * Récupération de l'instance de gestionnaire de file d'attente de mails.
     *
     * @return MailerQueueInterface
     */
    public function getQueue(): MailerQueueInterface;

    /**
     * Récupération d'un email.
     *
     * @param MailableInterface|array|null $mail Instance de l'email|Paramètre d'email|Email courant.
     *
     * @return MailableInterface
     */
    public function mailable($mail = null): MailableInterface;

    /**
     * Préparation du mail pour l'expédition.
     *
     * @return static
     */
    public function prepare(): MailerInterface;

    /**
     * Mise en file du message.
     *
     * @param MailableInterface|array|null $mail Instance de l'email|Paramètre d'email|Email courant si null.
     * @param DateTimeInterface|string $date
     * @param array $context
     *
     * @return int
     */
    public function queue($mail = null, $date = 'now', array $context = []): int;

    /**
     * Réinitialisation du pilote d'expédition des emails.
     *
     * @return void
     */
    public function resetDriver(): void;

    /**
     * Chemin absolu vers une ressource (fichier|répertoire).
     *
     * @param string|null $path Chemin relatif vers la ressource.
     *
     * @return string
     */
    public function resources(?string $path = null): string;

    /**
     * Envoi d'un message.
     *
     * @param MailableInterface|array|null $attrs Instance de l'email|Paramètres d'email|Email courant.
     *
     * @return boolean
     */
    public function send($attrs = null): bool;

    /**
     * Définition du chemin absolu vers le répertoire des ressources.
     *
     * @param string $resourceBaseDir
     *
     * @return static
     */
    public function setResourcesBaseDir(string $resourceBaseDir): MailerInterface;
}