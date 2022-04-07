<?php
/**
 * @package		Joomla.Plugins
 * @subpackage	System.J2xml
 *
 * @author		Helios Ciancio <info (at) eshiol (dot) it>
 * @link		http://www.eshiol.it
 * @copyright	Copyright (C) 2010 - 2020 Helios Ciancio. All Rights Reserved
 * @license		http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL v3
 * J2XML is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License
 * or other free or open source software licenses.
 */
defined('_JEXEC') or die();

jimport('eshiol.core.send');
jimport('eshiol.core.standard2');

/**
 *
 * @version __DEPLOY_VERSION__
 * @since 1.5.2
 */
class plgSystemJ2xml extends JPlugin
{

	/**
	 * Load the language file on instantiation.
	 *
	 * @var boolean
	 * @since  3.1
	 */
	protected $autoloadLanguage = true;

	/**
	 * Constructor
	 *
	 * @param object $subject
	 *        	The object to observe
	 * @param array $config
	 *        	An array that holds the plugin configuration
	 */
	function __construct (&$subject, $config)
	{
		parent::__construct($subject, $config);

		if ($this->params->get('debug') || defined('JDEBUG') && JDEBUG)
		{
			JLog::addLogger(array(
					'text_file' => $this->params->get('log', 'eshiol.log.php'),
					'extension' => 'plg_system_j2xml_file'
			), JLog::ALL, array(
					'plg_system_j2xml'
			));
		}
		if (PHP_SAPI == 'cli')
		{
			JLog::addLogger(array(
				'logger' => 'echo',
				'extension' => 'plg_system_j2xml'
			), JLog::ALL & ~ JLog::DEBUG, array(
				'plg_system_j2xml'
			));
		}
		else
		{
			JLog::addLogger(
				array(
					'logger' => ($this->params->get('logger') !== null) ? $this->params->get('logger') : 'messagequeue',
					'extension' => 'plg_system_j2xml'
				), JLog::ALL & ~ JLog::DEBUG, array(
					'plg_system_j2xml'
				));
			if ($this->params->get('phpconsole') && class_exists('LogLoggerPhpconsole'))
			{
				JLog::addLogger(array(
					'logger' => 'phpconsole',
					'extension' => 'plg_system_j2xml_phpconsole'
				), JLog::DEBUG, array(
					'plg_system_j2xml'
				));
			}
		}

		// Joomla! 3.0 compatibility
		$this->loadLanguage();
	}

	/**
	 * Method is called by index.php and administrator/index.php
	 *
	 * @access public
	 */
	public function onAfterDispatch ()
	{
		$app = JFactory::getApplication();
		if ($app->getName() != 'administrator')
		{
			return true;
		}

		$enabled = JComponentHelper::getComponent('com_j2xml', true);
		if (! $enabled->enabled)
		{
			return true;
		}

		$jinput = JFactory::getApplication()->input;
		$option = $jinput->get('option');
		$view = $jinput->get('view');
		$extension = $jinput->get('extension');

		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select($db->quoteName('enabled'))
			->from($db->quoteName('#__extensions'))
			->where($db->quoteName('type') . ' = ' . $db->quote('library'));
		$version = new \JVersion();
		if ($version->isCompatible('3.9'))
		{
			$query->where($db->quoteName('element') . ' = ' . $db->quote('eshiol/J2xmlpro'));
		}
		else
		{
			$query->where($db->quoteName('element') . ' = ' . $db->quote('J2xmlpro'));
		}
		$pro = (bool) $db->setQuery($query)->loadResult();

		if (($option == 'com_content') && (! $view || $view == 'articles' || $view == 'featured') ||
				($option == 'com_users') && (! $view || $view == 'users' || $view == 'levels') || ($option == 'com_weblinks') && (! $view || $view == 'weblinks') ||
				($option == 'com_categories') && ($extension == 'com_content') && (! $view || $view == 'categories') ||
				($option == 'com_contact') && (! $view || $view == 'contacts') ||
				$pro && ($option == 'com_menus') && (! $view || $view == 'menus') ||
				$pro && ($option == 'com_modules') && (! $view || $view == 'modules') ||
				($option == 'com_fields') && (! $view || $view == 'fields'))
		{
			$toolbar = JToolBar::getInstance('toolbar');

			if (($option == 'com_users') && ($view == 'levels'))
			{
				$control = 'viewlevels';
			}
			else
			{
				$control = substr($option, 4);
			}
			
			// Backward Joomla! 2.5
			if (version_compare(JPlatform::RELEASE, '12', 'ge'))
			{
				$toolbar->appendButton('Standard2', 'download', 'PLG_SYSTEM_J2XML_BUTTON_EXPORT', "j2xml.{$control}.export", true);
				$websites = self::getWebsites();
				if ($n = count($websites))
				{
					for ($i = 0; $i < $n; $i ++)
					{
						$websites[$i]->url = "index.php?option=com_j2xml&task={$control}.send&w_id=" . $websites[$i]->id;
					}
					$toolbar->appendButton('Send', 'out', 'PLG_SYSTEM_J2XML_BUTTON_SEND', $websites, true);
				}
			}
			else
			{
				$doc = JFactory::getDocument();
				$toolbar->prependButton('Separator', 'divider');
				$websites = self::getWebsites();
				if (count($websites))
				{
					$doc->addStyleDeclaration(".icon-32-j2xml_send{background:url(../media/plg_system_j2xml/images/icon-32-send.png) no-repeat;}");
					$toolbar->prependButton('Send', 'j2xml_send', 'PLG_SYSTEM_J2XML_BUTTON_SEND', "j2xml.{$control}.send", 'websites');
				}
				$doc->addStyleDeclaration(".icon-32-j2xml_export{background:url(../media/plg_system_j2xml/images/icon-32-export.png) no-repeat;}");
				$toolbar->prependButton('Standard2', 'j2xml_export', 'PLG_SYSTEM_J2XML_BUTTON_EXPORT', "j2xml.{$control}.export");
			}
		}

		// Trigger the onAfterDispatch event.
		// JPluginHelper::importPlugin('j2xml');
		// JFactory::getApplication()->triggerEvent('onLoadJS');

		return true;
	}

	/**
	 * Method to return a list of all websites
	 *
	 * @return array List of websites (empty array if none).
	 */
	private static function getWebsites ()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('id, title')
			->from('#__j2xml_websites')
			->where('state = 1');
		$db->setQuery($query);
		try
		{
			$websites = $db->loadObjectList();
		}
		catch (Exception $e)
		{
			$websites = null;
		}
		return $websites;
	}
}