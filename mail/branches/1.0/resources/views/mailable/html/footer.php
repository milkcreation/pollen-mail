<?php
/**
 * @var Pollen\Mail\MailableViewTemplateInterface $this
 */
?>
<?php if ($this->param('infos')) : ?>
    <tr class="rowFooterContent">
        <td>
            <?php if ($this->param('infos.company.name')) : ?>
                <b><?php echo strtoupper($this->param('infos.company.name')); ?></b>
                <br>
            <?php endif; ?>

            <?php if (
                $this->param('infos.contact.address1')||
                $this->param('infos.contact.address2') ||
                $this->param('infos.contact.address3') ||
                $this->param('infos.contact.postcode') ||
                $this->param('infos.contact.city')
            ): ?>
                <span class="unstyle-auto-detected-links">
                    <?php echo join(' ', array_filter([
                        $this->param('infos.contact.address1', ''),
                        $this->param('infos.contact.address2', ''),
                        $this->param('infos.contact.address3', ''),
                        $this->param('infos.contact.postcode', ''),
                        strtoupper($this->param('infos.contact.city', '')),
                    ])); ?>
                </span>
                <br>
            <?php endif; ?>

            <?php if ($this->param('infos.contact.phone') || $this->param('infos.contact.fax')) : ?>
                <span class="unstyle-auto-detected-links">
                    <?php echo implode(' - ', array_filter([
                        ($phone = $this->param('infos.contact.phone')) ? sprintf(__('Tél : %s', 'tify'), $phone) : '',
                        ($fax = $this->param('infos.contact.fax')) ? sprintf(__('Fax : %s', 'tify'), $fax) : '',
                    ])); ?>
                </span>
            <?php endif; ?>
        </td>
    </tr>
<?php endif;