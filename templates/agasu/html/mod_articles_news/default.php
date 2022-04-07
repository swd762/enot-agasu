<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_articles_news
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
// set params to get introtext with images
$params->set('image', 1);
$list = ModArticlesNewsHelper::getList($params);
?>
<div class="news-content row <?php echo $moduleclass_sfx; ?>">
    <?php foreach ($list as $item) : ?>
        <?php require JModuleHelper::getLayoutPath('mod_articles_news', '_item'); ?>
    <?php endforeach; ?>
</div>
