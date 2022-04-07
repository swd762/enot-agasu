<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die ;

jimport('joomla.form.formfield');

class JFormFieldExtensionLink extends JFormField
{
	public $type = 'ExtensionLink';

	protected $link_type;
	protected $link;

	protected function getLabel()
	{
		$html = '';

		$lang = JFactory::getLanguage();
		$lang->load('plg_system_jqueryeasy', JPATH_SITE);

		JHtml::_('bootstrap.tooltip');

		$class = '';

		switch ($this->link_type) {
			case 'forum': $image = 'forum.png'; $title = 'PLG_SYSTEM_JQUERYEASY_EXTENSIONLINK_FORUM_LABEL'; break;
			case 'forumbeta': $image = 'forum-beta.png'; $title = 'PLG_SYSTEM_JQUERYEASY_EXTENSIONLINK_FORUM_LABEL'; $class = 'btn-inverse'; break;
			case 'demo': $image = 'demo.png'; $title = 'PLG_SYSTEM_JQUERYEASY_EXTENSIONLINK_DEMO_LABEL'; break;
			case 'review': $image = 'review.png'; $title = 'PLG_SYSTEM_JQUERYEASY_EXTENSIONLINK_REVIEW_LABEL'; break;
			case 'donate': $image = 'paypal.png'; $title = 'PLG_SYSTEM_JQUERYEASY_EXTENSIONLINK_DONATE_LABEL'; break;
			case 'upgrade': $image = 'upgrade.png'; $title = 'PLG_SYSTEM_JQUERYEASY_EXTENSIONLINK_UPGRADE_LABEL'; break;
			case 'doc': $image = 'documentation.png'; $title = 'PLG_SYSTEM_JQUERYEASY_EXTENSIONLINK_DOC_LABEL'; break;
			case 'onlinedoc': $image = 'documentation.png'; $title = 'PLG_SYSTEM_JQUERYEASY_EXTENSIONLINK_ONLINEDOC_LABEL'; break;
			case 'report': $image = 'bug-report.png'; $title = 'PLG_SYSTEM_JQUERYEASY_EXTENSIONLINK_BUGREPORT_LABEL'; break;
			case 'support': $image = 'support.png'; $title = 'PLG_SYSTEM_JQUERYEASY_EXTENSIONLINK_SUPPORT_LABEL'; break;
			case 'translate': $image = 'translate.png'; $title = 'PLG_SYSTEM_JQUERYEASY_EXTENSIONLINK_TRANSLATE_LABEL'; break;
			default: $image = ''; $title = '';
		}

		if ($this->link) {
		    $html .= '<a class="btn hasTooltip' . ($class == '' ? '' : ' ' . $class) . '" title="'.JText::_($title).'" href="'.$this->link.'" target="_blank">';
		} else {
		    $html .= '<span class="label hasTooltip' . ($class == '' ? '' : ' ' . $class) . '" title="'.JText::_($title).'">';
		}
		if ($image) {
			$html .= '<img src="'.JURI::root().'plugins/system/jqueryeasy/images/'.$image.'">';
		} else {
			$html .= JText::_($title);
		}
		if ($this->link) {
		    $html .= '</a>';
		} else {
		    $html .= '</span>';
		}

		return $html;
	}

	protected function getInput()
	{
		$lang = JFactory::getLanguage();
		$lang->load('plg_system_jqueryeasy', JPATH_SITE);

		$html = '<div class="syw_info" style="padding-top: 5px; overflow: inherit">';

		if ($this->description) {
			if ($this->link) {
				$html .= JText::sprintf($this->description, $this->link);
			} else {
				$html .= JText::_($this->description);
			}
		} else {

			switch ($this->link_type) {
				case 'forum': $image = true; $desc = 'PLG_SYSTEM_JQUERYEASY_EXTENSIONLINK_FORUM_DESC'; break;
				case 'forumbeta': $image = true; $desc = 'PLG_SYSTEM_JQUERYEASY_EXTENSIONLINK_FORUMBETA_DESC'; break;
				case 'demo': $image = true; $desc = 'PLG_SYSTEM_JQUERYEASY_EXTENSIONLINK_DEMO_DESC'; break;
				case 'review': $image = true; $desc = 'PLG_SYSTEM_JQUERYEASY_EXTENSIONLINK_REVIEW_DESC'; break;
				case 'donate': $image = true; $desc = 'PLG_SYSTEM_JQUERYEASY_EXTENSIONLINK_DONATE_DESC'; break;
				case 'upgrade': $image = true; $desc = 'PLG_SYSTEM_JQUERYEASY_EXTENSIONLINK_UPGRADE_DESC'; break;
				case 'doc': $image = true; $desc = 'PLG_SYSTEM_JQUERYEASY_EXTENSIONLINK_DOC_DESC'; break;
				case 'onlinedoc': $image = true; $desc = 'PLG_SYSTEM_JQUERYEASY_EXTENSIONLINK_ONLINEDOC_DESC'; break;
				case 'report': $image = true; $desc = 'PLG_SYSTEM_JQUERYEASY_EXTENSIONLINK_BUGREPORT_DESC'; break;
				case 'support': $image = true; $desc = 'PLG_SYSTEM_JQUERYEASY_EXTENSIONLINK_SUPPORT_DESC'; break;
				case 'translate': $image = true; $desc = 'PLG_SYSTEM_JQUERYEASY_EXTENSIONLINK_TRANSLATE_DESC'; break;
				default: $desc = '';
			}

			if ($desc) {
				if ($this->link) {
					$html .= JText::sprintf($desc, $this->link);
				} else {
					$html .= JText::_($desc);
				}
			}
		}

		if ($this->link_type == 'review') {
			$html = rtrim($html, '.');
			$html .= ' <a href="'.$this->link.'" target="_blank" style="text-decoration: none; vertical-align: text-bottom">';
			$html .= '<span class="icon-star" style="color: #fcac0a; margin: 0; vertical-align: middle"></span>';
			$html .= '<span class="icon-star" style="color: #fcac0a; margin: 0; vertical-align: middle"></span>';
			$html .= '<span class="icon-star" style="color: #fcac0a; margin: 0; vertical-align: middle"></span>';
			$html .= '<span class="icon-star" style="color: #fcac0a; margin: 0; vertical-align: middle"></span>';
			$html .= '<span class="icon-star" style="color: #fcac0a; margin: 0; vertical-align: middle"></span></a> .';
		}

		$html .= '</div>';

		return $html;
	}

	public function setup(SimpleXMLElement $element, $value, $group = null)
	{
		$return = parent::setup($element, $value, $group);

		if ($return) {
			$this->link_type = $this->element['linktype'];
			$this->link = isset($this->element['link']) ? $this->element['link'] : '';
		}

		return $return;
	}

}
?>
