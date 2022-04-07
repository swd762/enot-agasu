<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_search
 *
 * @copyright   (C) 2006 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Including fallback code for the placeholder attribute in the search field.
JHtml::_('jquery.framework');
JHtml::_('script', 'system/html5fallback.js', array('version' => 'auto', 'relative' => true, 'conditional' => 'lt IE 9'));

if ($width) {
    $moduleclass_sfx .= ' ' . 'mod_search' . $module->id;
    $css = 'div.mod_search' . $module->id . ' input[type="search"]{ width:auto; }';
    JFactory::getDocument()->addStyleDeclaration($css);
    $width = ' size="' . $width . '"';
} else {
    $width = '';
}
?>

<a href="#" class="header-search-btn ic-search"></a>
<form action="<?php echo JRoute::_('index.php'); ?>" method="post" class="form-inline search header-search">
    <?php
    $output = '<button class="search-submit-btn ic-search"></button>';
    $output .= '<input name="searchword" id="mod-search-searchword' . $module->id . '" maxlength="' . $maxlength . '"  class="inputbox search-query input-medium" type="search"'
        . $width;
    $output .= ' placeholder="' . $text . '" />';
    $output .= '<span class="search-close-btn bi bi-x-lg"></span>';
    echo $output;
    ?>
    <input type="hidden" name="task" value="search"/>
    <input type="hidden" name="option" value="com_search"/>
    <input type="hidden" name="Itemid" value="<?php echo $mitemid; ?>"/>
</form>
