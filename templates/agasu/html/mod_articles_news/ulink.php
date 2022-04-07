<?php
/**
 * @package     ${NAMESPACE}
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */

defined('_JEXEC') or die;
// set params to get introtext with images
$params->set('image', 1);
$list = ModArticlesNewsHelper::getList($params);
?>
<div class="container">
    <header class="block-header useful-links__header">
        <?php if ($module->showtitle) { ?>
            <h3><?php echo $module->title; ?></h3>
        <?php } ?>
    </header>
    <div class="useful-links-slider">
        <?php foreach ($list as $item) :
            require JModuleHelper::getLayoutPath('mod_articles_news', '_ulinks_item');
        endforeach; ?>
    </div>

</div>
