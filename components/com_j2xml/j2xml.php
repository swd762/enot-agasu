<?php
/**
 * @package		J2XML
 * @subpackage	com_j2xml
 *
 * @version		__DEPLOY_VERSION__
 * @since		1.7.0.64
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

$params = JComponentHelper::getParams('com_j2xml');

jimport('joomla.log.log');
if ($params->get('debug') || defined('JDEBUG') && JDEBUG)
{
	JLog::addLogger(array(
		'text_file' => $params->get('log', 'eshiol.log.php'),
		'extension' => 'com_j2xml_file'
	), JLog::DEBUG, array(
		'lib_j2xml',
		'com_j2xml'
	));
}

$jinput = JFactory::getApplication()->input;
$controllerClass = 'J2XMLController';
$task = $jinput->getCmd('task', 'services');

if (strpos($task, '.') === false)
	$controllerPath = JPATH_COMPONENT . '/controller.php';
else
{
	// We have a defined controller/task pair -- lets split them out
	list ($controllerName, $task) = explode('.', $task);

	// Define the controller name and path
	$controllerName = strtolower($controllerName);

	$controllerPath = JPATH_COMPONENT . '/controllers/' . $controllerName;

	$format = $jinput->getCmd('format');
	if ($format == 'xmlrpc')
	{
		require_once JPATH_LIBRARIES . '/eshiol/phpxmlrpc/Log/Logger/XmlrpcLogger.php';

		include_once JPATH_LIBRARIES . '/eshiol/phpxmlrpc/lib/xmlrpc.inc';
		include_once JPATH_LIBRARIES . '/eshiol/phpxmlrpc/lib/xmlrpcs.inc';

		JLog::addLogger(array(
				'logger' => 'xmlrpc',
				'extension' => 'com_j2xml',
				'service' => 'XMLRPCJ2XMLServices'
		), JLog::ALL & ~ JLog::DEBUG, array(
				'lib_j2xml',
				'com_j2xml'
		));
		$controllerPath .= '.' . strtolower($format);
	}
	else
	{
		JLog::addLogger(array(
				'logger' => 'messagequeue',
				'extension' => 'com_j2xml'
		), JLog::ALL & ~ JLog::DEBUG, array(
				'lib_j2xml',
				'com_j2xml'
		));
		if ($this->params->get('phpconsole') && class_exists('JLogLoggerPhpconsole'))
		{
			JLog::addLogger(array(
				'logger' => 'phpconsole',
				'extension' => 'com_j2xml_phpconsole'
			), JLog::DEBUG, array(
				'lib_j2xml',
				'com_j2xml'
			));
		}
	}
	$controllerPath .= '.php';
	// Set the name for the controller and instantiate it
	$controllerClass .= ucfirst($controllerName);
}

// If the controller file path exists, include it ... else lets die with a 500
// error
if (file_exists($controllerPath))
{
	require_once ($controllerPath);
}
else
{
	throw new Exception('Invalid Controller ' . $controllerName, 500);
}

if (class_exists($controllerClass))
{
	$controller = new $controllerClass();
}
else
{
	throw new Exception('Invalid Controller Class - ' . $controllerName, 500);
}

// $config = JFactory::getConfig();

// Perform the Request task
$controller->execute($task);

// Redirect if set by the controller
$controller->redirect();
