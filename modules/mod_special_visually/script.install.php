<?php
defined('_JEXEC') or die;

class mod_special_visuallyInstallerScript {
	private $min_joomla_version = '3.4.0'; // Минимальная версия Joomla
	private $min_php_version = '5.6'; // Минимальная версия PHP
	
	// Перед установкой
	function preflight($type, $parent) {
		if (!$this->passMinimumJoomlaVersion()) { // Проверим минимальную версию Joomla
			return false;
		}
		if (!$this->passMinimumPHPVersion()) { // Проверим минимальную версию PHP
			return false;
		}
		return true;
	}
	
	// После установки
	function postflight($type, $parent) {
		$changelog = $this->getChangelog(); // Получим и разберем Changelog
		JFactory::getApplication()->enqueueMessage($changelog, 'notice'); // Покажем Changelog в виде уведомления
		return true;
	}
	
	// Функция проверки минимальной версии Joomla
	private function passMinimumJoomlaVersion() {
		if (version_compare(JVERSION, $this->min_joomla_version, '<')) {
			JFactory::getApplication()->enqueueMessage(
				JText::sprintf(
					'MOD_SPECIAL_VISUALLY_NOT_COMPATIBLE_UPDATE',
					'<strong>' . JVERSION . '</strong>',
					'<strong>' . $this->min_joomla_version . '</strong>'
				),
				'error'
			);
			return false;
		}
		return true;
	}
	
	// Функция проверки минимальной версии PHP
	private function passMinimumPHPVersion() {
		if (version_compare(PHP_VERSION, $this->min_php_version, 'l')) {
			JFactory::getApplication()->enqueueMessage(
				JText::sprintf(
					'MOD_SPECIAL_VISUALLY_NOT_COMPATIBLE_PHP',
					'<strong>' . PHP_VERSION . '</strong>',
					'<strong>' . $this->min_php_version . '</strong>'
				),
				'error'
			);
			return false;
		}
		return true;
	}
	
	// Функция генерации Changelog
	private function getChangelog() {
		if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
		
		$changelog = file_get_contents(JPATH_SITE . DS . 'modules' . DS . 'mod_special_visually/CHANGELOG.txt');
		
		$changelog = "\n" . trim(preg_replace('#^.* \*/#s', '', $changelog));
		$changelog = preg_replace("#\r#s", '', $changelog);
		
		$parts = explode("\n\n", $changelog);
		if (empty($parts)) {
			return '';
		}
		
		$changelog = [];
		$changelog[] = array_shift($parts);
		
		foreach ($parts as $part) {
			$part = trim($part);
			if (!preg_match('#^[0-9]+-[a-z]+-[0-9]+ : v([0-9\.]+)\n#i', $part, $matches)) {
				continue;
			}
			$version = $matches[1];
			$changelog[] = $part;
		}
		
		$changelog = implode("\n\n", $changelog);
		
		$change_types = [ //  + Added   ! Removed   ^ Changed   # Fixed
			'+' => ['Added', 'success'],
			'!' => ['Removed', 'danger'],
			'^' => ['Changed', 'warning'],
			'#' => ['Fixed', 'info'],
		];
		foreach ($change_types as $char => $type) {
			$changelog = preg_replace(
				'#\n ' . preg_quote($char, '#') . ' #',
				"\n" . '<span class="label label-sm label-' . $type[1] . '" title="' . $type[0] . '">' . $char . '</span> ',
				$changelog
			);
		}
		
		$changelog = preg_replace(
			"#(\n+)([0-9]+.*?) : .*?([0-9]+\.[0-9]+\.[0-9]+)([^\n]*?\n+)#",
			'</pre>\1'
			. '<h3><span class="label label-inverse" style="font-size: 0.8em;">v\3</span>'
			. ' <small>\2</small></h3>'
			. '\4<pre>',
			$changelog
		);
		
		$changelog = str_replace(
			[
				'<pre>',
				'[EXP]',
				'[PRO]',
			],
			[
				'<pre style="line-height: 1.6em;">',
				'<span class="badge badge-sm badge-important">EXP</span>',
				'<span class="badge badge-sm badge-info">PRO</span>',
			],
			$changelog
		);
		
		$changelog = preg_replace(
			'#\[J([1-9][\.0-9]*)\]#',
			'<span class="badge badge-sm badge-default">J\1</span>',
			$changelog
		);
		
		return '<h3>' . JText::_('MOD_SPECIAL_VISUALLY_LATEST_CHANGES') . ':</h3><div style="max-height: 240px; padding-right: 20px; margin-right: -20px; overflow: auto;">' . $changelog . '</div>';
	}
}