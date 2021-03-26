<?php

declare(strict_types=1);

namespace Pollen\Mail;

use InvalidArgumentException;
use Pollen\Http\Response;
use Pollen\Http\ResponseInterface;
use Pollen\Support\Concerns\BuildableTrait;
use Pollen\Support\ParamsBag;
use Pollen\Support\Concerns\ParamsBagAwareTrait;
use Pollen\Support\Proxy\MailProxy;
use Pollen\Support\Proxy\PartialProxy;
use Pollen\View\ViewEngine;
use Pollen\View\ViewEngineInterface;
use RuntimeException;

class Mailable implements MailableInterface
{
    use BuildableTrait;
    use MailProxy;
    use ParamsBagAwareTrait;
    use PartialProxy;

    /**
     * Moteur d'expédition du mail.
     * @var MailerDriverInterface
     */
    private $mailer;

    /**
     * Langue du message.
     * @var string
     */
    protected $locale;

    /**
     * Expéditeur du message.
     * @var array
     */
    protected $from;

    /**
     * Liste des destinataires du message.
     * @var array
     */
    protected $to;

    /**
     * Liste des destinataires en copie carbone.
     * @var array
     */
    protected $cc;

    /**
     * Liste des destinataires en copie carbone.
     * @var array
     */
    protected $bcc;

    /**
     * Liste des destinataire de la réponse au message.
     * @var array
     */
    protected $replyTo;

    /**
     * Liste des pièces jointes au messages.
     * @var array
     */
    protected $attachments;

    /**
     * Jeu des caractères du message.
     * @var string
     */
    protected $charset;

    /**
     * Encodage du message.
     * @var string
     */
    protected $encoding;

    /**
     * Typage du contenu du message.
     * @var string
     */
    protected $contentType;

    /**
     * Sujet du message.
     * @var string
     */
    protected $subject;

    /**
     * Contenu du message au format HTML.
     * @var string
     */
    protected $html;

    /**
     * Contenu du message au format texte brut.
     * @var string
     */
    protected $text;

    /**
     * Activation du formatage des propriétés CSS dans les balises HTML.
     * @var bool
     */
    protected $inlineCss;

    /**
     * Propriétés CSS du message au format HTML.
     * @var string
     */
    protected $css;

    /**
     * Instance des données associées aux gabarits du message.
     * @var ParamsBag
     */
    protected $datasBag;

    /**
     * Instance du moteur de gabarits d'affichage.
     * @var ViewEngineInterface
     */
    protected $viewEngine;

    /**
     * @param MailManagerInterface|null $mailManager
     */
    public function __construct(?MailManagerInterface $mailManager = null)
    {
        if ($mailManager !== null) {
            $this->setMailManager($mailManager);
        }
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return $this->message();
    }

    /**
     * @inheritDoc
     */
    public function build(): MailableInterface
    {
        if (!$this->isBuilt()) {
            $this->buildParams();
            $this->buildMailer();

            $this->setBuilt();
        }

        return $this;
    }

    /**
     * Préparation des paramètres d'expédition du mail.
     *
     * @return void
     */
    protected function buildParams(): void
    {
        if ($this->from === null && ($from = $this->params('from'))) {
            $this->setFrom($from);
        }

        if ($this->to === null && ($to = $this->params('to'))) {
            $this->setTo($to);
        }

        if ($this->replyTo === null && ($replyTo = $this->params('reply-to'))) {
            $this->setReplyTo($replyTo);
        }

        if ($this->bcc === null && ($bcc = $this->params('bcc'))) {
            $this->setBcc($bcc);
        }

        if ($this->cc === null && ($cc = $this->params('cc'))) {
            $this->setCc($cc);
        }

        if ($this->attachments === null && ($attachments = $this->params('attachments', []))) {
            $this->setAttachments($attachments);
        }

        if ($this->locale === null && ($locale = $this->params('locale'))) {
            $this->setLocale($locale);
        }

        if ($this->charset === null && ($charset = $this->params('charset'))) {
            $this->setCharset($charset);
        }

        if ($this->encoding === null && ($encoding = $this->params('encoding'))) {
            $this->setEncoding($encoding);
        }

        if ($this->contentType === null && ($contentType = $this->params('content_type'))) {
            $this->setContentType($contentType);
        }

        if ($this->subject === null && ($subject = $this->params('subject'))) {
            $this->setSubject($subject);
        }

        if ($datas = $this->params('datas', [])) {
            $this->datas(array_merge($this->mail()->defaults('datas', []), $datas));
        }

        if ($this->html === null && ($html = $this->params('html'))) {
            if (is_array($html)) {
                if ($body = $html['body'] ?? true) {
                    $body = is_string($body) ? $body : $this->view('html/body', $this->datas()->all());
                }

                if ($header = $html['header'] ?? true) {
                    $header = is_string($header) ? $header : $this->view('html/header', $this->datas()->all());
                }

                if ($footer = $html['footer'] ?? true) {
                    $footer = is_string($footer) ? $footer : $this->view('html/footer', $this->datas()->all());
                }
                $html = $this->view(
                    'html/message',
                    array_merge($this->datas()->all(), compact('body', 'header', 'footer'))
                );
            } else {
                $html = is_string($html) ? $html : $this->view('html/message', $this->datas()->all());
            }
        } elseif ($this->html === null) {
            $html = $this->params('text') ?: $this->view('html/message', $this->datas()->all());
        } else {
            $html = $this->html;

            if (!MailManager::hasHtmlHead($html)) {
                $html = $this->view('html/message', array_merge($this->datas()->all(), ['body' => $html]));
            }
        }

        if ($this->text === null && ($text = $this->params('text'))) {
            $this->setText($text);
        } elseif ($this->text === null) {
            $this->setText(MailManager::htmlToText($html ?: $this->view('text/message', $this->datas()->all())));
        }

        if ($this->inlineCss === null && $this->params('inline_css')) {
            $this->setInlineCss();
        }

        if ($this->css === null && ($css = $this->params('css'))) {
            $this->setCss($css);
        }

        if ($this->css && $this->inlineCss) {
            try {
                $html = MailManager::htmlInlineCss($html, $this->css);
            } catch (RuntimeException $e) {
                unset($e);
            }
        }

        $this->setHtml($html);
    }

    /**
     * Préparation des paramètres du moteur d'expédition du mail.
     *
     * @return void
     */
    protected function buildMailer(): void
    {
        $mailer = $this->getMailer();

        if ($this->from !== null) {
            $mailer->setFrom(...$this->from);
        }

        if ($this->to !== null) {
            foreach ($this->to as $to) {
                $mailer->addTo(...$to);
            }
        }

        if ($this->replyTo !== null) {
            foreach ($this->replyTo as $replyTo) {
                $mailer->addReplyTo(...$replyTo);
            }
        }

        if ($this->bcc !== null) {
            foreach ($this->bcc as $bcc) {
                $mailer->addBcc(...$bcc);
            }
        }

        if ($this->cc !== null) {
            foreach ($this->cc as $cc) {
                $mailer->addCc(...$cc);
            }
        }

        if ($this->attachments !== null) {
            foreach ($this->attachments as $attachment) {
                $mailer->addAttachment($attachment);
            }
        }

        if ($this->charset !== null) {
            $mailer->setCharset($this->charset);
        }

        if ($this->charset !== null) {
            $mailer->setEncoding($this->encoding);
        }

        if ($this->contentType !== null) {
            $mailer->setContentType($this->contentType);
        }

        if ($this->subject !== null) {
            $mailer->setSubject($this->subject);
        }

        switch ($this->contentType) {
            default:
            case 'multipart/alternative' :
                $mailer->setHtml($this->html);
                $mailer->setText($this->text);
                break;
            case 'text/html' :
                $mailer->setHtml($this->text);
                break;
            case 'text/plain' :
                $mailer->setText($this->text);
                break;
        }
    }

    /**
     * @inheritDoc
     */
    public function datas($key = null, $default = null)
    {
        if (!$this->datasBag instanceof ParamsBag) {
            $this->datasBag = new ParamsBag();
        }

        if (is_null($key)) {
            return $this->datasBag;
        }

        if (is_string($key)) {
            return $this->datasBag->get($key, $default);
        }

        if (is_array($key)) {
            $this->datasBag->set($key);
            return $this->datasBag;
        }

        throw new InvalidArgumentException('Invalid Mailable DatasBag passed method arguments');
    }

    /**
     * @inheritDoc
     */
    public function defaultParams(): array
    {
        return array_merge(
            [
                /**
                 * Destinataires du message.
                 * @var string|array
                 */
                'to'           => [],
                /**
                 * Expéditeur du message (requis).
                 * @var string|array
                 */
                'from'         => [],
                /**
                 * Destinataire de la réponse au message.
                 * @var string|array
                 */
                'reply-to'     => [],
                /**
                 * Destinataires en copie cachée.
                 * @var string|array
                 */
                'bcc'          => [],
                /**
                 * Destinataire en copie carbone.
                 * @var string|array
                 */
                'cc'           => [],
                /**
                 * Liste des pièces jointes.
                 * @var string|array
                 */
                'attachments'  => [],
                /**
                 * Format HTML du message.
                 * @var bool|string|array
                 */
                'html'         => true,
                /**
                 * Format texte du message.
                 * @var string
                 */
                'text'         => '',
                /**
                 * Liste des données associées aux gabarits du message.
                 * @var array
                 */
                'datas'         => [],
                /**
                 * Objet du message.
                 * @var string
                 */
                'subject'      => 'Test d\'envoi de mail',
                /**
                 * Langue du message
                 * @var string
                 */
                'locale'       => 'en',
                /**
                 * Jeu de caractères.
                 * @var string
                 */
                'charset'      => 'utf-8',
                /**
                 * Encodage.
                 * @var string
                 */
                'encoding'     => '8bit',
                /**
                 * Typage du contenu.
                 * @var string
                 */
                'content_type' => 'multipart/alternative',
                /**
                 * Propriétés CSS du message HTML.
                 * @var string
                 */
                'css'          => file_get_contents($this->mail()->resources('/assets/css/styles.css')),
                /**
                 * Activation du formatage des propriétés CSS dans les balises HTML.
                 */
                'inline_css'   => true,
                /**
                 * Définition des paramètres du moteur d'affichage des gabarits.
                 * @var array
                 */
                'viewer'       => [],
            ],
            $this->mail()->defaults()->all()
        );
    }

    /**
     * @inheritDoc
     */
    public function debug(): string
    {
        $this->mail($this->build());

        return $this->view(
            'debug',
            ['html' => $this->html, 'text' => $this->text]
        ); /*: $this->mailer()->getDriver()->error();*/
    }

    /**
     * @inheritDoc
     */
    public function getLocale(): string
    {
        return $this->locale ?? 'en';
    }

    /**
     * @inheritDoc
     */
    public function getMailer(): MailerDriverInterface
    {
        if ($this->mailer === null) {
            $this->mailer = $this->mail()->getMailer();
        }
        return $this->mailer;
    }

    /**
     * @inheritDoc
     */
    public function queue($date = 'now', array $params = []): int
    {
        $queueFactory = $this->mail()->getQueueFactory();

        $this->mail($this->build());

        return $queueFactory->add($this, $date, $params);
    }

    /**
     * @inheritDoc
     */
    public function message(): string
    {
        $this->mail($this->build());

        return $this->getMailer()->getMessage();
    }

    /**
     * @inheritDoc
     */
    public function response(): ResponseInterface
    {
        return new Response($this->message());
    }

    /**
     * @inheritDoc
     */
    public function send(): bool
    {
        $this->mail($this->build());

        return $this->getMailer()->send();
    }

    /**
     * @inheritDoc
     */
    public function setAttachments($attachments): MailableInterface
    {
        $this->attachments = MailManager::parseAttachment($attachments);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setBcc($bcc): MailableInterface
    {
        $this->bcc = MailManager::parseContact($bcc);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setCc($cc): MailableInterface
    {
        $this->cc = MailManager::parseContact($cc);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setCharset(string $charset): MailableInterface
    {
        $this->charset = $charset;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setContentType(string $contentType): MailableInterface
    {
        $this->contentType = $contentType;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setCss(string $css): MailableInterface
    {
        $this->css = $css;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setEncoding(string $encoding): MailableInterface
    {
        $this->encoding = $encoding;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setFrom($from): MailableInterface
    {
        $this->from = MailManager::parseContact($from)[0];

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setHtml(string $html): MailableInterface
    {
        $this->html = $html;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setInlineCss(bool $inlineCss = true): MailableInterface
    {
        $this->inlineCss = $inlineCss;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setLocale(string $locale): MailableInterface
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setMailer(MailerDriverInterface $mailer): MailableInterface
    {
        $this->mailer = $mailer;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setReplyTo($replyTo): MailableInterface
    {
        $this->replyTo = MailManager::parseContact($replyTo);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setSubject(string $subject): MailableInterface
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setText(string $text): MailableInterface
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setTo($to): MailableInterface
    {
        $this->to = MailManager::parseContact($to);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function view(?string $view = null, array $data = [])
    {
        if (is_null($this->viewEngine)) {
            $directory = null;
            $overrideDir = null;
            $default = $this->mail()->config('default.viewer', []);

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
                $directory = $this->mail()->resources('/views/mailable');
                if (!file_exists($directory)) {
                    throw new InvalidArgumentException(
                        'Mailable must have an accessible view directory'
                    );
                }
            }

            $this->viewEngine = new ViewEngine();
            if ($container = $this->mail()->getContainer()) {
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
