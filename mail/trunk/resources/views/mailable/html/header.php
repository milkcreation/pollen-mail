<?php
/**
 * @var Pollen\Mail\MailableViewTemplateInterface $this
 */
?>
<?php if ($logo = $this->param('infos.logo')) : ?>
    <tr class="rowHeaderContent">
        <td>
            <?php echo is_array($logo) ? partial('tag', [
                'attrs' => [
                    'class'  => 'BodyHeader-logo',
                    'src'    => $logo['src'] ?? '',
                    'width'  => $logo['width'] ?? 200,
                    'height' => $logo['height'] ?? 40,
                    'alt'    => $logo['alt'] ?? __('Logo', 'tify'),
                    'border' => 0,
                ],
                'tag'   => 'img',
            ]) : $logo; ?>
        </td>
    </tr>
<?php endif;