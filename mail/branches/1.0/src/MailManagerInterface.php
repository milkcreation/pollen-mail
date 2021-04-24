<?php

declare(strict_types=1);

namespace Pollen\Mail;

use DateTimeInterface;
use InvalidArgumentException;
use Pollen\Support\Concerns\BootableTraitInterface;
use Pollen\Support\Concerns\ConfigBagAwareTraitInterface;
use Pollen\Support\Concerns\ResourcesAwareTraitInterface;
use Pollen\Support\ParamsBag;
use Pollen\Support\Proxy\ContainerProxyInterface;

interface MailManagerInterface extends
    BootableTraitInterface,
    ConfigBagAwareTraitInterface,
    ResourcesAwareTraitInterface,
    ContainerProxyInterface
{
    /**
     * Chargement.
     *
     * @return static
     */
    public function boot(): MailManagerInterface;

    /**
     * Affichage du message en mode débogage.
     *
     * @param MailableInterface|array|null $mailableDef Instance de l'email|Paramètres d'email|Email courant.
     *
     * @return void
     */
    public function debug($mailableDef = null): void;

    /**
     * Définition|Récupération|Instance des paramètres par défaut des mails.
     *
     * @param array|string|null $key
     * @param mixed $default
     *
     * @return string|int|array|mixed|ParamsBag
     *
     * @throws InvalidArgumentException
     */
    public function defaults($key = null, $default = null);

    /**
     * Récupération de l'instance de mail associée.
     *
     * @param string|null $name
     *
     * @return MailableInterface
     */
    public function getMailable(?string $name = null): ?MailableInterface;

    /**
     * Récupération d'une nouvelle instance du moteur d'expédition mails.
     *
     * @return MailerDriverInterface
     */
    public function getMailer(): MailerDriverInterface;

    /**
     * Récupération de l'instance de gestionnaire de file d'attente de mails.
     *
     * @return MailQueueFactoryInterface
     */
    public function getQueueFactory(): MailQueueFactoryInterface;

    /**
     * Vérification d'existence d'une instance de mail associée.
     *
     * @return bool
     */
    public function hasMailable(): bool;

    /**
     * Mise en file du message.
     *
     * @param MailableInterface|array|null $mailableDef Instance de l'email|Paramètre d'email|Email courant si null.
     * @param DateTimeInterface|string $date
     * @param array $context
     *
     * @return int
     */
    public function queue($mailableDef = null, $date = 'now', array $context = []): int;

    /**
     * Envoi d'un message.
     *
     * @param MailableInterface|array|null $mailableDef Instance de l'email|Paramètres d'email|Email courant.
     *
     * @return boolean
     */
    public function send($mailableDef = null): bool;

    /**
     * Définition de la liste des paramètres globaux par défaut des emails.
     *
     * @param array $attrs
     *
     * @return static
     */
    public function setDefaults(array $attrs): MailManagerInterface;

    /**
     * Récupération d'un email.
     *
     * @param MailableInterface|string|array|null $mailableDef Instance de l'email|Paramètre d'email|Email courant.
     *
     * @return MailManagerInterface
     */
    public function setMailable($mailableDef = null): MailManagerInterface;

    /**
     * Définition de la fonction de rappel de configuration du pilote d'expédition.
     *
     * @param callable $mailerConfigCallback
     *
     * @return static
     */
    public function setMailerConfigCallback(callable $mailerConfigCallback): MailManagerInterface;
}