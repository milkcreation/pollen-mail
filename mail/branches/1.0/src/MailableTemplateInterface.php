<?php

declare(strict_types=1);

namespace Pollen\Mail;

use Pollen\ViewExtends\PlatesTemplateInterface;

/**
 * @method array getAttachments()
 * @method array getBcc()
 * @method array getCc()
 * @method string getCharset()
 * @method string getContentType()
 * @method string getEncoding()
 * @method array getFrom()
 * @method array getHeaders()
 * @method string getHtml()
 * @method string getLocale()
 * @method string getMessage()
 * @method array getReplyTo()
 * @method string getSubject()
 * @method string getText()
 * @method array getTo()
 * @method bool hasHtml()
 * @method bool hasText()
 * @method bool linearizeContacts()
 */
interface MailableTemplateInterface extends PlatesTemplateInterface
{
}