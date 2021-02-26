<?php

declare(strict_types=1);

namespace Pollen\Mail;

use Pollen\View\ViewEngineInterface;
use Pollen\View\ViewTemplateInterface;

interface MailableViewEngineInterface extends ViewEngineInterface
{
    /**
     * {@inheritDoc}
     *
     * @return MailableViewTemplateInterface
     */
    public function make($name): ViewTemplateInterface;
}