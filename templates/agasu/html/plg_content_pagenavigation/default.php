<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Content.pagenavigation
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$lang = JFactory::getLanguage(); ?>

<ul class="pager pagenav">
    <?php if ($row->prev) :
        $direction = $lang->isRtl() ? 'right' : 'left'; ?>
        <li class="previous">
<!--            <a href="--><?php //echo $row->prev; ?><!--" rel="prev" class="link-btn">-->
<!--                --><?php //echo '<i class="bi bi-arrow-' . $direction . '"></i> ' . $row->prev_label; ?>
<!--            </a>-->
            <a href="<?php echo $row->prev; ?>" rel="prev" class="link-btn">
                <?php echo '<i class="bi bi-arrow-' . $direction . '-short"></i> ' . $row->prev_label; ?>
            </a>
        </li>
    <?php endif; ?>
    <?php if ($row->next) :
        $direction = $lang->isRtl() ? 'left' : 'right'; ?>
        <li class="next">
            <a href="<?php echo $row->next; ?>" rel="next" class="link-btn">
                <?php echo $row->next_label . ' <i class="bi bi-arrow-' . $direction . '-short"></i>'; ?>
            </a>
        </li>
    <?php endif; ?>
</ul>
