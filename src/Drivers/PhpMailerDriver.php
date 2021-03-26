<?php

declare(strict_types=1);

namespace Pollen\Mail\Drivers;

use PHPMailer\PHPMailer\PHPMailer;
use Pollen\Mail\MailerDriverInterface;
use Exception;

class PhpMailerDriver implements MailerDriverInterface
{
    /**
     * Instance du pilote de traitement des emails.
     * @var PHPMailer
     */
    protected $phpMailer;

    /**
     * CONSTRUCTEUR.
     *
     * @param PHPMailer|null $phpmailer Instance de PHPMailer.
     *
     * @return void
     */
    public function __construct(?PHPMailer $phpmailer = null)
    {
        $this->phpMailer = $phpmailer ?: new PHPMailer();
    }

    /**
     * {@inheritDoc}
     *
     * @throws Exception
     */
    public function addAttachment(string $path): MailerDriverInterface
    {
        $args = func_get_args();

        $this->phpMailer->addAttachment(...$args);

        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @throws Exception
     */
    public function addBcc(string $email, string $name = ''): MailerDriverInterface
    {
        $this->phpMailer->addBCC($email, $name);

        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @throws Exception
     */
    public function addCc(string $email, string $name = ''): MailerDriverInterface
    {
        $this->phpMailer->addCC($email, $name);

        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @throws Exception
     */
    public function addReplyTo(string $email, string $name = ''): MailerDriverInterface
    {
        $this->phpMailer->addReplyTo($email, $name);

        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @throws Exception
     */
    public function addTo(string $email, string $name = ''): MailerDriverInterface
    {
        $this->phpMailer->addAddress($email, $name);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function error(): string
    {
        return $this->phpMailer->ErrorInfo;
    }

    /**
     * @inheritDoc
     */
    public function getAttachments(): array
    {
        return $this->phpMailer->getAttachments();
    }

    /**
     * @inheritDoc
     */
    public function getBcc(): array
    {
        return $this->phpMailer->getBccAddresses();
    }

    /**
     * @inheritDoc
     */
    public function getCharset(): string
    {
        return $this->phpMailer->CharSet;
    }

    /**
     * @inheritDoc
     */
    public function getCc(): array
    {
        return $this->phpMailer->getCcAddresses();
    }

    /**
     * @inheritDoc
     */
    public function getContentType(): string
    {
        return $this->phpMailer->ContentType;
    }

    /**
     * @inheritDoc
     */
    public function getEncoding(): string
    {
        return $this->phpMailer->Encoding;
    }

    /**
     * @inheritDoc
     */
    public function getFrom(): array
    {
        return [$this->phpMailer->From, $this->phpMailer->FromName];
    }

    /**
     * @inheritDoc
     */
    public function getHeaders(): array
    {
        return explode($this->phpMailer::getLE(), $this->phpMailer->createHeader());
    }

    /**
     * @inheritDoc
     */
    public function getHtml(): string
    {
        if (!$this->hasHtml()) {
            return '';
        }
        return $this->phpMailer->Body;
    }

    /**
     * @inheritDoc
     */
    public function getMessage(): string
    {
        return $this->phpMailer->Body;
    }

    /**
     * @inheritDoc
     */
    public function getReplyTo(): array
    {
        return $this->phpMailer->getReplyToAddresses();
    }

    /**
     * @inheritDoc
     */
    public function getSubject(): string
    {
        return $this->phpMailer->Subject;
    }

    /**
     * @inheritDoc
     */
    public function getText(): string
    {
        if (!$this->hasText()) {
            return '';
        }
        return $this->hasHtml() ? $this->phpMailer->AltBody : $this->phpMailer->Body;
    }

    /**
     * @inheritDoc
     */
    public function getTo(): array
    {
        return $this->phpMailer->getToAddresses();
    }

    /**
     * @inheritDoc
     */
    public function hasHtml(): bool
    {
        return in_array($this->getContentType(), ['text/html', 'multipart/alternative']);
    }

    /**
     * @inheritDoc
     */
    public function hasText(): bool
    {
        return in_array($this->getContentType(), ['text/plain', 'multipart/alternative']);
    }

    /**
     * {@inheritDoc}
     *
     * @throws Exception
     */
    public function prepare(): bool
    {
        return $this->phpMailer->preSend();
    }

    /**
     * {@inheritDoc}
     *
     * @throws Exception
     */
    public function send(): bool
    {
        return $this->phpMailer->send();
    }

    /**
     * @inheritDoc
     */
    public function setCharset(string $charset = 'utf-8'): MailerDriverInterface
    {
        $this->phpMailer->CharSet = $charset;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setContentType(string $content_type = 'multipart/alternative'): MailerDriverInterface
    {
        $this->phpMailer->ContentType = in_array($content_type, ['text/html', 'text/plain', 'multipart/alternative'])
            ? $content_type : 'multipart/alternative';

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setEncoding(string $encoding): MailerDriverInterface
    {
        $this->phpMailer->Encoding = in_array($encoding, ['8bit', '7bit', 'binary', 'base64', 'quoted-printable'])
            ? $encoding : '8bit';

        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @throws Exception
     */
    public function setFrom(string $email, string $name = ''): MailerDriverInterface
    {
        $this->phpMailer->setFrom($email, $name);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setHtml(string $message): MailerDriverInterface
    {
        $this->phpMailer->Body = $message;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setSubject(string $subject = ''): MailerDriverInterface
    {
        $this->phpMailer->Subject = $subject;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setText(string $text): MailerDriverInterface
    {
        $this->phpMailer->AltBody = $text;

        return $this;
    }
}