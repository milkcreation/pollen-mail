<?php

declare(strict_types=1);

namespace Pollen\Mail;

use InvalidArgumentException;
use Pollen\Http\Response;
use Pollen\Http\ResponseInterface;
use Pollen\Support\ParamsBag;
use Pollen\Support\Concerns\ParamsBagAwareTrait;
use Pollen\Support\Proxy\MailerProxy;
use Pollen\Support\Proxy\PartialProxy;
use Pollen\View\ViewEngine;
use Pollen\View\ViewEngineInterface;

class Mailable implements MailableInterface
{
    use MailerProxy;
    use ParamsBagAwareTrait;
    use PartialProxy;

    /**
     * Instance des données de message.
     * @var ParamsBag
     */
    public $data;

    /**
     * Instance du moteur de gabarits d'affichage.
     * @var ViewEngineInterface
     */
    protected $viewEngine;

    /**
     * @param MailerInterface|null $mailer
     */
    public function __construct(?MailerInterface $mailer = null)
    {
        if ($mailer !== null) {
            $this->setMailer($mailer);
        }
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return $this->render();
    }

    /**
     * @inheritDoc
     */
    public function data($key, $value = null): MailableInterface
    {
        if (!$this->data instanceof ParamsBag) {
            $this->data = new ParamsBag();
        }

        if (!is_array($key)) {
            $key = [$key => $value];
        }

        $this->data->set($key);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function defaults(): array
    {
        return $this->mailer()->getDefaults();
    }

    /**
     * @inheritDoc
     */
    public function debug(): string
    {
        $this->mailer()->mailable($this);

        return $this->mailer()->prepare() ? $this->view('debug') : $this->mailer()->getDriver()->error();
    }

    /**
     * @inheritDoc
     */
    public function queue($date = 'now', array $params = []): int
    {
        $this->mailer()->mailable($this);

        return $this->mailer()->prepare()->queue($this, $date, $params);
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        return $this->mailer()->prepare()->getDriver()->getHtml();
    }

    /**
     * @inheritDoc
     */
    public function response(): ResponseInterface
    {
        return new Response($this->render());
    }

    /**
     * @inheritDoc
     */
    public function send(): bool
    {
        $this->mailer()->mailable($this);

        return $this->mailer()->prepare()->getDriver()->send();
    }

    /**
     * @inheritDoc
     */
    public function view(?string $view = null, array $data = [])
    {
        if (is_null($this->viewEngine)) {
            $directory = null;
            $overrideDir = null;
            $default = $this->mailer()->config('default.viewer', []);

            $directory = $this->params('viewer.directory');
            if ($directory && !file_exists($directory)) {
                $directory = null;
            }

            $overrideDir = $this->params('viewer.override_dir');
            if ($overrideDir && !file_exists($overrideDir)) {
                $overrideDir = null;
            }

            if ($directory === null && isset($default['directory'])) {
                $default['directory'] = rtrim($default['directory'], '/');
                if (file_exists($default['directory'])) {
                    $directory = $default['directory'];
                }
            }

            if ($overrideDir === null && isset($default['override_dir'])) {
                $default['override_dir'] = rtrim($default['override_dir'], '/');
                if (file_exists($default['override_dir'])) {
                    $overrideDir = $default['override_dir'];
                }
            }

            if ($directory === null) {
                $directory = $this->mailer()->resources('/views/mailable');
                if (!file_exists($directory)) {
                    throw new InvalidArgumentException(
                        'Mailable must have an accessible view directory'
                    );
                }
            }

            $this->viewEngine = new ViewEngine();
            if ($container = $this->mailer()->getContainer()) {
                $this->viewEngine->setContainer($container);
            }

            $this->viewEngine->setDirectory($directory)->setDelegate($this)->setLoader(MailableViewLoader::class);

            if ($overrideDir !== null) {
                $this->viewEngine->addFolder('_override_dir', $overrideDir, true);
            }
        }

        if (func_num_args() === 0) {
            return $this->viewEngine;
        }

        return $this->viewEngine->render($view, $data);
    }
}
