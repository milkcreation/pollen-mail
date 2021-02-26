<?php

declare(strict_types=1);

namespace Pollen\Mail;

use Pollen\View\ViewEngine;
use Pollen\View\ViewTemplateInterface;

class MailableViewEngine extends ViewEngine implements MailableViewEngineInterface
{
    /**
     * {@inheritDoc}
     *
     * @return MailableViewTemplateInterface
     */
    public function make($name): ViewTemplateInterface
    {
        return new MailableViewTemplate($this, $name);
    }
}