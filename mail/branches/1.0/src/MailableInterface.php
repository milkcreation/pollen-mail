<?php

declare(strict_types=1);

namespace Pollen\Mail;

use DateTimeInterface;
use Pollen\Http\ResponseInterface;
use Pollen\Support\Concerns\ParamsBagAwareTraitInterface;
use Pollen\Support\Proxy\MailerProxyInterface;

interface MailableInterface extends MailerProxyInterface, ParamsBagAwareTraitInterface
{
    /**
     * Résolution de sortie de la classe sous la forme d'une chaîne de caractères.
     *
     * @return string
     */
    public function __toString(): string;

    /**
     * Définition des données du message.
     *
     * @param string|array $key
     * @param mixed $value
     *
     * @return static
     */
    public function data($key, $value = null): MailableInterface;

    /**
     * Liste des paramètres par défaut.
     *
     * @return array
     */
    public function defaults(): array;

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
     * Affichage de l'email.
     *
     * @return string
     */
    public function render(): string;

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
     * Récupération de l'affichage d'un gabarit.
     *
     * @param string|null $view
     * @param array $data
     *
     * @return string
     */
    public function view(?string $view = null, array $data = []): string;
}