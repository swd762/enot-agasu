<?php
/**
 * @package		J2XML
 * @subpackage	com_j2xml
 *
 * @author		Helios Ciancio <info (at) eshiol (dot) it>
 * @link		http://www.eshiol.it
 * @copyright	Copyright (C) 2010 - 2020 Helios Ciancio. All Rights Reserved
 * @license		http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL v3
 * J2XML is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// no direct access
defined('_JEXEC') or die('Restricted access.');

jimport('eshiol.J2xmlpro.Exporter');
jimport('eshiol.J2xml.Exporter');

/**
 * J2XML Component View
 *
 * @version __DEPLOY_VERSION__
 * @since 3.2.137
 */
class J2XMLView extends JViewLegacy
{

	function display ($tpl = null)
	{
		$app = JFactory::getApplication();
		$jinput = $app->input;
		$cid = $jinput->get('cid', null, 'RAW');
		$ids = explode(",", $cid);

		$params = JComponentHelper::getParams('com_j2xml');

		$options = array();
		$options['images'] = $params->get('export_images', '1');
		$options['users'] = $params->get('export_users', '1');
		$options['categories'] = 1;
		$options['contacts'] = $params->get('export_contacts', '1');

		if (class_exists('eshiol\J2xmlpro\Exporter'))
		{
			$exporter = new eshiol\J2xmlpro\Exporter();
		}
		else
		{
			$exporter = new eshiol\J2xml\Exporter();
		}

		$get_xml = strtolower(str_replace('J2XMLView', '', get_class($this)));
		try
		{
			$exporter->$get_xml($ids, $xml, $options);
		}
		catch (\Exception $ex)
		{
			JLog::add(JText::sprintf('LIB_J2XML_MSG_USERGROUP_ERROR', $ex->getMessage()), JLog::ERROR, 'lib_j2xml');
			$app->redirect('index.php?option=com_' . $get_xml);
			return;
		}

		$options = array();
		$options['debug'] = $params->get('debug', 0);
		$options['gzip'] = $params->get('export_gzip', '0');

		$exporter->export($xml, $options);
	}
}
?>