<?php

declare(strict_types=1);

namespace Pollen\Mail\Metabox;

use tiFy\Contracts\Mail\Mailer;
use tiFy\Metabox\Contracts\MetaboxContract;
use tiFy\Metabox\MetaboxDriver;

class MailConfigMetabox extends MetaboxDriver
{
    /**
     * Instance du gestionnaire de mail.
     * @var Mailer|null
     */
    private $mailer;

    /**
     * @inheritDoc
     */
    protected $name = 'mail_config';

    /**
     * @param Mailer $mailer
     * @param MetaboxContract $metaboxManager
     */
    public function __construct(Mailer $mailer, MetaboxContract $metaboxManager)
    {
        $this->mailer = $mailer;

        parent::__construct($metaboxManager);
    }

    /**
     * @inheritDoc
     */
    public function defaultParams(): array
    {
        return array_merge(
            parent::defaultParams(),
            [
                'enabled'    => [
                    'activation' => true,
                    'sender'     => true,
                    'recipients' => true,
                ],
                'info'       => '',
                'sender'     => [
                    'title' => __('Réglages de l\'expéditeur', 'tify'),
                ],
                'recipients' => [
                    'title' => __('Réglages des destinataires', 'tify'),
                ],
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function getDefaultValue()
    {
        return array_merge(['enabled' => true], $this->defaultValue ?: []);
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return $this->title ?? __('Paramètres du message', 'tify');
    }

    /**
     * Récupération de l'instance du gestionnaire de mail.
     *
     * @return Mailer|null
     */
    public function mailer(): ?Mailer
    {
        return $this->mailer;
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        $defaultMail = $this->mailer()->getDefaults('to');

        if (isset($defaultMail[0])) {
            if (!$this->get('sender.default.email')) {
                $this->set(['sender.default.email' => $defaultMail[0]]);
            }
            if (!$this->get('sender.default.name')) {
                $this->set(['sender.default.name' => $defaultMail[1] ?? '']);
            }
            if (!$this->get('sender.info')) {
                $this->set(
                    [
                        'sender.info' => sprintf(
                            __('Par défaut : %s', 'tify'),
                            join(
                                ' - ',
                                array_filter([$defaultMail[0], $defaultMail[1] ?? ''])
                            )
                        ),
                    ]
                );
            }
            if (!$this->get('recipients.default.email')) {
                $this->set(['recipients.default.email' => $defaultMail[0]]);
            }
            if (!$this->get('recipients.default.name')) {
                $this->set(['recipients.default.name' => $defaultMail[1] ?? '']);
            }
            if (!$this->get('recipients.info')) {
                $this->set(
                    [
                        'recipients.info' => sprintf(
                            __('Par défaut : %s', 'tify'),
                            join(
                                ' - ',
                                array_filter([$defaultMail[0], $defaultMail[1] ?? ''])
                            )
                        ),
                    ]
                );
            }
        }
        return parent::render();
    }

    /**
     * @inheritDoc
     */
    public function viewDirectory(): string
    {
        return $this->mailer()->resources('views/metabox/mail-config');
    }
}