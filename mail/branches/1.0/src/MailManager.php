<?php

declare(strict_types=1);

namespace Pollen\Mail;

use Html2Text\Html2Text;
use InvalidArgumentException;
use Pelago\Emogrifier\CssInliner;
use Pollen\Mail\Drivers\PhpMailerDriver;
use Pollen\Support\Arr;
use Pollen\Support\Concerns\BootableTrait;
use Pollen\Support\Concerns\ConfigBagAwareTrait;
use Pollen\Support\Filesystem;
use Pollen\Support\Proxy\ContainerProxy;
use Pollen\Support\ParamsBag;
use Pollen\Validation\Validator as v;
use Psr\Container\ContainerInterface as Container;
use RuntimeException;
use Symfony\Component\DomCrawler\Crawler;
use Throwable;

class MailManager implements MailManagerInterface
{
    use BootableTrait;
    use ConfigBagAwareTrait;
    use ContainerProxy;

    /**
     * Instance principale.
     * @var static|null
     */
    private static $instance;

    /**
     * Instance du mail courant.
     * @var MailableInterface|null
     */
    protected $mailable;

    /**
     * Instance des mail déclarés.
     * @var MailableInterface[]|array
     */
    protected $mailables = [];

    /**
     * Instance du gestionnaire de mise en file.
     * @var MailQueueFactoryInterface
     */
    protected $queueFactory;

    /**
     * Chemin vers le répertoire des ressources.
     * @var string|null
     */
    protected $resourcesBaseDir;

    /**
     * Liste des paramètres de mail par défaut.
     * @var ParamsBag
     */
    protected $defaultsBag;

    /**
     * @param array $config
     * @param Container|null $container
     *
     * @return void
     */
    public function __construct(array $config = [], ?Container $container = null)
    {
        $this->setConfig($config);

        if ($container !== null) {
            $this->setContainer($container);
        }

        if (!self::$instance instanceof static) {
            self::$instance = $this;
        }
    }

    /**
     * Récupération de l'instance principale.
     *
     * @return static
     */
    public static function getInstance(): MailManagerInterface
    {
        if (self::$instance instanceof self) {
            return self::$instance;
        }
        throw new RuntimeException(sprintf('Unavailable [%s] instance', __CLASS__));
    }

    /**
     * Vérification d'existence d'une balise HEAD dans le HTML.
     *
     * @param string $html
     *
     * @return bool
     */
    public static function hasHtmlHead(string $html): bool
    {
        return (bool)(new Crawler($html))->filter('head')->count();
    }

    /**
     * Conversion du HTML en texte brut.
     *
     * @param string $html
     * @param string $css
     *
     * @return string
     */
    public static function htmlInlineCss(string $html, string $css): string
    {
        try {
            return CssInliner::fromHtml($html)->inlineCss($css)->render();
        } catch (Throwable $e) {
            throw new RuntimeException('Mailer Html Css Inliner throws an exception', 0, $e);
        }
    }

    /**
     * Conversion du HTML en texte brut.
     *
     * @param string $html
     *
     * @return string
     */
    public static function htmlToText(string $html): string
    {
        return (new Html2Text($html))->getText();
    }

    /**
     * Traitement récursif d'une liste de contacts.
     *
     * @param string|string[]|array $contact Liste de contact.
     *
     * @return array|null
     */
    public static function parseContact($contact): ?array
    {
        $output = (func_num_args() === 2) ? func_get_arg(1) : [];

        if (is_string($contact)) {
            $email = '';
            $name = '';
            $bracket_pos = strpos($contact, '<');
            if ($bracket_pos !== false) {
                if ($bracket_pos > 0) {
                    $name = substr($contact, 0, $bracket_pos - 1);
                    $name = str_replace('"', '', $name);
                    $name = trim($name);
                }

                $email = substr($contact, $bracket_pos + 1);
                $email = str_replace('>', '', $email);
                $email = trim($email);
            } elseif (!empty($contact)) {
                $email = $contact;
            }

            if ($email && v::email()->validate($email)) {
                $output[] = array_filter([$email, $name]);
            }
        } elseif (is_array($contact)) {
            if (!Arr::isAssoc($contact)) {
                if ((count($contact) === 2) && is_string($contact[0]) && is_string($contact[1]) &&
                    v::email()->validate($contact[0]) && !v::email()->validate($contact[1])
                ) {
                    $output[] = $contact;
                } else {
                    foreach ($contact as $c) {
                        if ($value = static::parseContact($c, $output)) {
                            $output = $value;
                        }
                    }
                }
            } else {
                $email = $contact['email'] ?? null;

                if (v::email()->validate($email)) {
                    $output[] = array_filter([$email, $contact['name'] ?? null]);
                }
            }
        }

        return array_filter($output) ?: null;
    }

    /**
     * Traitement récursif d'une liste de pièces jointes.
     *
     * @param string|string[]|array $attachment
     *
     * @return array
     */
    public static function parseAttachment($attachment): array
    {
        $output = (func_num_args() === 2) ? func_get_arg(1) : [];

        if (is_string($attachment)) {
            if (is_file($attachment)) {
                $output[] = $attachment;
            }
        } elseif (is_array($attachment)) {
            foreach ($attachment as $a) {
                if (is_string($a)) {
                    $output = static::parseAttachment($a, $output);
                } elseif (is_array($a)) {
                    $filename = $a[0] ?? null;

                    if ($filename && is_file($filename)) {
                        $output[] = $a;
                    }
                }
            }
        }

        return $output;
    }

    /**
     * @inheritDoc
     */
    public function boot(): MailManagerInterface
    {
        if (!$this->isBooted()) {
            $this->setBooted();
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function debug($mailableDef = null): void
    {
        $this->setMailable($mailableDef);

        echo $this->mailable->debug();
        exit;
    }

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
    public function defaults($key = null, $default = null)
    {
        if (!$this->defaultsBag instanceof ParamsBag) {
            $this->defaultsBag = new ParamsBag();
        }

        if (is_null($key)) {
            return $this->defaultsBag;
        }

        if (is_string($key)) {
            return $this->defaultsBag->get($key, $default);
        }

        if (is_array($key)) {
            $this->defaultsBag->set($key);
            return $this->defaultsBag;
        }

        throw new InvalidArgumentException('Invalid Mailer DefaultsBag passed method arguments');
    }

    /**
     * @inheritDoc
     */
    public function getMailable(?string $name = null): ?MailableInterface
    {
        if (is_null($name)) {
            return $this->mailable;
        }
        return $this->mailables[$name] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function getMailer(): MailerDriverInterface
    {
        return $this->containerHas(MailerDriverInterface::class)
            ? $this->containerGet(MailerDriverInterface::class) : new PhpMailerDriver();
    }

    /**
     * @inheritDoc
     */
    public function getQueueFactory(): MailQueueFactoryInterface
    {
        if ($this->queueFactory === null) {
            $this->queueFactory = $this->containerHas(MailQueueFactoryInterface::class)
                ? $this->containerGet(MailQueueFactoryInterface::class) : new MailQueueFactory($this);
        }

        return $this->queueFactory;
    }

    /**
     * @inheritDoc
     */
    public function hasMailable(): bool
    {
        return (bool)$this->mailable;
    }

    /**
     * @inheritDoc
     */
    public function queue($mailableDef = null, $date = 'now', array $context = []): int
    {
        $this->setMailable($mailableDef);

        return $this->mailable->queue($date, $context);
    }

    /**
     * @inheritDoc
     */
    public function resources(?string $path = null): string
    {
        if ($this->resourcesBaseDir === null) {
            $this->resourcesBaseDir = Filesystem::normalizePath(
                realpath(dirname(__DIR__) . '/resources/')
            );

            if (!file_exists($this->resourcesBaseDir)) {
                throw new RuntimeException('Field ressources directory unreachable');
            }
        }

        return is_null($path) ? $this->resourcesBaseDir : $this->resourcesBaseDir . Filesystem::normalizePath($path);
    }

    /**
     * @inheritDoc
     */
    public function send($mailableDef = null): bool
    {
        $this->setMailable($mailableDef);

        return $this->mailable->send();
    }

    /**
     * @inheritDoc
     */
    public function setDefaults(array $attrs): MailManagerInterface
    {
        $this->defaults($attrs);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setMailable($mailableDef = null): MailManagerInterface
    {
        if ($this->mailable instanceof MailableInterface && $mailableDef === null) {
            return $this;
        }

        if ($mailableDef instanceof Mailable) {
            $this->mailable = $mailableDef;

            return $this;
        }

        if (is_string($mailableDef)) {
            if ($mailable = $this->getMailable($mailableDef)) {
                $this->mailable = $mailable;

                return $this;
            }
            throw new RuntimeException(sprintf('Mailable [%s] does not exists', $mailableDef));
        }

        $mailableParams = array_merge(
            $this->config()->all(),
            is_array($mailableDef) ? $mailableDef : []
        );

        $this->mailable = $this->containerHas(MailableInterface::class)
            ? $this->containerGet(MailableInterface::class) : new Mailable($this);

        $this->mailable->setParams($mailableParams);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setResourcesBaseDir(string $resourceBaseDir): MailManagerInterface
    {
        $this->resourcesBaseDir = Filesystem::normalizePath($resourceBaseDir);

        return $this;
    }
}