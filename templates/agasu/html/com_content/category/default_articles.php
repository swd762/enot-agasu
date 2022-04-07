<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$app = JFactory::$application;
$templatePath = $app->getTemplate();
$utilClassPath = join(DIRECTORY_SEPARATOR, array(JPATH_THEMES, $templatePath, 'libs', 'util.php'));
// add util class
require_once($utilClassPath);

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

// Create some shortcuts.
$params = $this->item->params;
$n = count($this->items);
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));

// Check for at least one editable article
$isEditable = false;

if (!empty($this->items)) {
    foreach ($this->items as $article) {
        if ($article->params->get('access-edit')) {
            $isEditable = true;
            break;
        }
    }
}
?>

<?php if (empty($this->items)) : ?>

    <?php if ($this->params->get('show_no_articles', 1)) : ?>
        <p><?php echo JText::_('COM_CONTENT_NO_ARTICLES'); ?></p>
    <?php endif; ?>

<?php else : ?>

<form action="<?php echo htmlspecialchars(JUri::getInstance()->toString()); ?>" method="post" name="adminForm" id="adminForm" class="form-inline">
    <!--	--><?php //if ($this->params->get('show_headings') || $this->params->get('filter_field') != 'hide' || $this->params->get('show_pagination_limit')) :?>
    <!--	<fieldset class="filters btn-toolbar clearfix">-->
    <!--		--><?php //if ($this->params->get('filter_field') != 'hide') :?>
    <!--			<div class="btn-group">-->
    <!--				<label class="filter-search-lbl element-invisible" for="filter-search">-->
    <!--					--><?php //echo JText::_('COM_CONTENT_' . $this->params->get('filter_field') . '_FILTER_LABEL') . '&#160;'; ?>
    <!--				</label>-->
    <!--				<input type="text" name="filter-search" id="filter-search" value="-->
    <?php //echo $this->escape($this->state->get('list.filter')); ?><!--" class="inputbox" onchange="document.adminForm.submit();" title="-->
    <?php //echo JText::_('COM_CONTENT_FILTER_SEARCH_DESC'); ?><!--" placeholder="--><?php //echo JText::_('COM_CONTENT_' . $this->params->get('filter_field') . '_FILTER_LABEL'); ?><!--" />-->
    <!--			</div>-->
    <!--		--><?php //endif; ?>
    <!--		--><?php //if ($this->params->get('show_pagination_limit')) : ?>
    <!--			<div class="btn-group pull-right">-->
    <!--				<label for="limit" class="element-invisible">-->
    <!--					--><?php //echo JText::_('JGLOBAL_DISPLAY_NUM'); ?>
    <!--				</label>-->
    <!--				--><?php //echo $this->pagination->getLimitBox(); ?>
    <!--			</div>-->
    <!--		--><?php //endif; ?>
    <!---->
    <!--		<input type="hidden" name="filter_order" value="" />-->
    <!--		<input type="hidden" name="filter_order_Dir" value="" />-->
    <!--		<input type="hidden" name="limitstart" value="" />-->
    <!--		<input type="hidden" name="task" value="" />-->
    <!--	</fieldset>-->
    <!--	--><?php //endif; ?>

    <?php
    $headerTitle = '';
    $headerDate = '';
    $headerAuthor = '';
    $headerHits = '';
    $headerEdit = '';
    ?>
    <?php foreach ($this->items as $i => $article) : ?>
        <?php if ($this->items[$i]->state == 0) : ?>
            <tr class="system-unpublished cat-list-row<?php echo $i % 2; ?>">
        <?php else: ?>
            <tr class="cat-list-row<?php echo $i % 2; ?>" >
        <?php endif; ?>
        <?php
        /*display image for news category. If not image - add logo*/
        if (in_array($article->access, $this->user->getAuthorisedViewLevels())) :
            if ($article->catid == 177) :
                $output = preg_match_all('/<img[^>]+src=([\'"])?((?(1).+?|[^\s>]+))(?(1)\1)/', $article->introtext, $imgs);
                if (!$imgs[2][0])
                    $imgs[2][0] = '/files/images/44-redaktor/logo/agasu_logo1.png';
                ?>
                <a class=" news-link row" href="<?php echo JRoute::_(ContentHelperRoute::getArticleRoute($article->slug, $article->catid, $article->language)); ?>">
                    <?php
                    if ($imgs[2][0]) : ?>
                        <div class="col-xl-3 news-image-cont"><img class="news-image" src="<?php echo $imgs[2][0]; ?>"></div>
                    <?php else : ?>
                        <div class="news-image-cont"><img class="news-image" src="/files/images/64-admin/agasu_l ogo_foot.png"></div>
                    <?php endif;
                    echo '<div class="col-xl-8 news-content-cont">';
                    echo '<h4>' . $this->escape($article->title) . '</h4>';

                    // обработка текста для вывода в списке новостей, далее унести в отдельный класс думаю

                    $article->introtext = preg_replace('/<img[^>]*>/', '', $article->introtext);
                    $introtext = $article->introtext;
                    $introtext = str_replace(array('<p>', '</p>'), ' ', $introtext);
                    $introtext = strip_tags($introtext);
                    $length = 300;
                    $encoding = 'UTF-8';
                    $postfix = '...';
                    $introtext = Util::getShortDescription($introtext, $length, $encoding, $postfix);

                    $dateText = Util::getDateRusString($article);


                    echo '<span>' . $introtext . '</span>';
                    echo '<p  style="font-style: italic;margin-top: 5px">' . $dateText . '</p>';
                    echo '<p class="link-btn" style="margin-top: 10px">' . 'Подробнее' . '</p>';
                    echo '</div>';
                    ?>

                </a>
            <?php
            else : ?>
                <a class="content-material-link" href="<?php echo JRoute::_(ContentHelperRoute::getArticleRoute($article->slug, $article->catid, $article->language)); ?>">
                    <?php
                    echo $this->escape($article->title); ?>
                </a>
            <?php endif; else: ?>
            <?php
            echo $this->escape($article->title) . ' : ';
            $menu = JFactory::getApplication()->getMenu();
            $active = $menu->getActive();
            $itemId = $active->id;
            $link = new JUri(JRoute::_('index.php?option=com_users&view=login&Itemid=' . $itemId, false));
            $link->setVar('return', base64_encode(JRoute::_(ContentHelperRoute::getArticleRoute($article->slug, $article->catid, $article->language), false)));
            ?>
            <a href="<?php echo $link; ?>" class="register">
                <?php echo JText::_('COM_CONTENT_REGISTER_TO_READ_MORE'); ?>
            </a>
        <?php endif; ?>
        <?php if ($this->params->get('list_show_date')) : ?>
            <td <?php echo $headerDate; ?> class="list-date small">
                <?php
                echo JHtml::_(
                    'date', $article->displayDate,
                    $this->escape($this->params->get('date_format', JText::_('DATE_FORMAT_LC3')))
                ); ?>
            </td>
        <?php endif; ?>
        <?php if ($this->params->get('list_show_author', 1)) : ?>
            <td <?php echo $headerAuthor; ?> class="list-author">
                <?php if (!empty($article->author) || !empty($article->created_by_alias)) : ?>
                    <?php $author = $article->author ?>
                    <?php $author = ($article->created_by_alias ? $article->created_by_alias : $author); ?>
                    <?php if (!empty($article->contact_link) && $this->params->get('link_author') == true) : ?>
                        <?php echo JText::sprintf('COM_CONTENT_WRITTEN_BY', JHtml::_('link', $article->contact_link, $author)); ?>
                    <?php else: ?>
                        <?php echo JText::sprintf('COM_CONTENT_WRITTEN_BY', $author); ?>
                    <?php endif; ?>
                <?php endif; ?>
            </td>
        <?php endif; ?>
        <?php if ($this->params->get('list_show_hits', 1)) : ?>
            <td <?php echo $headerHits; ?> class="list-hits">
							<span class="badge badge-info">
								<?php echo JText::sprintf('JGLOBAL_HITS_COUNT', $article->hits); ?>
							</span>
            </td>
        <?php endif; ?>
        <?php if ($isEditable) : ?>
            <td <?php echo $headerEdit; ?> class="list-edit">
                <?php if ($article->params->get('access-edit')) : ?>
                    <?php echo JHtml::_('icon.edit', $article, $params); ?>
                <?php endif; ?>
            </td>
        <?php endif; ?>
        </tr>
    <?php endforeach; ?>
    </tbody>
    </table>
    <?php endif; ?>

    <?php // Code to add a link to submit an article. ?>
    <?php if ($this->category->getParams()->get('access-create')) : ?>
        <?php echo JHtml::_('icon.create', $this->category, $this->category->params); ?>
    <?php endif; ?>

    <?php // Add pagination links ?>
    <?php if (!empty($this->items)) : ?>
    <?php if (($this->params->def('show_pagination', 2) == 1 || ($this->params->get('show_pagination') == 2)) && ($this->pagination->pagesTotal > 1)) : ?>
        <div class="pagination">

            <?php if ($this->params->def('show_pagination_results', 1)) : ?>
                <p class="counter pull-right">
                    <?php echo $this->pagination->getPagesCounter(); ?>
                </p>
            <?php endif; ?>

            <?php echo $this->pagination->getPagesLinks(); ?>
        </div>
    <?php endif; ?>
</form>
<?php endif; ?>
