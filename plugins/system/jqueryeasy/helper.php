<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

class plgJQueryEasyHelper
{
    static public function isEnabledOnPage($params, $suffix = '')
    {
//         if (JFactory::getApplication()->isAdmin()) {
//             return false;
//         }
        
        // enable the plugin for HTML pages only        
        if (JFactory::getDocument()->getType() !== 'html') {
            // put here so JFactory::getDocument() does not break feeds (will break if used in any function before onAfterRoute)
            // https://groups.google.com/forum/?fromgroups#!topic/joomla-dev-general/S0GYKhLm92A
            return false;
        }
        
        if (JFactory::getApplication()->getTemplate() === 'system') {
            return false;
        }
        
        // device selection
        
//         if (($params->get('device'.$suffix, '') === 'desktop' && $is_mobile) || ($params->get('device'.$suffix, '') === 'mobile' && !$is_mobile)) {
//             return false;
//         }
        
        // template selection
        
        $templates_inex = $params->get('template_inex'.$suffix, '');
        
        if ($templates_inex !== '') {
            
            $templates = self::getParamValues($params->get('templateid'.$suffix, array()));
            
            if ($templates) {
                
                if ((int)$templates_inex === 1) { // include : use the plugin if in template
                    
                    if (!in_array(JFactory::getApplication()->getTemplate(true)->id, $templates)) {
                        return false;
                    }
                } else { // exclude : plugin is excluded if in template
                    
                    if (in_array(JFactory::getApplication()->getTemplate(true)->id, $templates)) {
                        return false;
                    }
                }
            }
        }
        
        // component selection
        
        $components_inex = $params->get('wherecomponent_inex'.$suffix, '');
        
        if ($components_inex !== '') {
            
            $components = self::getParamValues($params->get('wherecomponent'.$suffix, array()));
            
            if ($components) {
                
                if ((int)$components_inex === 1) { // include : use the plugin if on extension's page
                    
                    if (!in_array(JFactory::getApplication()->input->get('option', ''), $components)) {
                        return false;
                    }
                } else { // exclude : plugin is excluded if on extension's page
                    
                    if (in_array(JFactory::getApplication()->input->get('option', ''), $components)) {
                        return false;
                    }
                }
            }
        }

        // page selection
        
        $urls_inex = $params->get('url_inex'.$suffix, '');
        
        if ($urls_inex !== '') {
            
            $url_paths = trim( (string) $params->get('url_inex_items'.$suffix, ''));
            
            if ($url_paths) {
                
                if ((int)$urls_inex === 1) { // include : use the plugin if on a page
                    
                    $paths = array_map('trim', (array) explode("\n", $url_paths));
                    
                    foreach ($paths as $path) {
                        if (self::paths_are_identical(JUri::current(), $path)) {
                            
                            return true;
                        }
                    }
                    
                    return false;
                    
                } else { // exclude: plugin is excluded if on a page
                    
                    $paths = array_map('trim', (array) explode("\n", $url_paths));
                    
                    foreach ($paths as $path) {
                        if (self::paths_are_identical(JUri::current(), $path)) {
                            
                            return false;
                        }
                    }
                }
            }
        }

        return true;
    }
    
    static public function getParamValues($array_of_elements = array())
    {
        if (isset($array_of_elements) && !empty($array_of_elements)) {
            
            if (!is_array($array_of_elements)) { // before the plugin is saved, the value is a string
                $array_of_elements = trim($array_of_elements) !== '' ? array($array_of_elements) : array();
            }
            
            $array_of_element_values = array_count_values($array_of_elements);
            if (isset($array_of_element_values['all']) && $array_of_element_values['all'] > 0) { // 'all' was selected
                return array();
            } else {
                return $array_of_elements;
            }
        }
        
        return array();
    }

    static public function getRegularExpression($type, $name)
    {
        switch ($name . '_' . $type) {
            case 'jquery_js': return '([\\/a-zA-Z0-9_:\.~-]*)jquery([0-9\.-]|latest|core|min|pack)*?.js(.*?)';
            case 'jqueryui_js': return '([\\/a-zA-Z0-9_:\.~-]*)jquery[.-]*ui([0-9\.-]|latest|core|custom|min|pack)*?.js(.*?)';
            case 'noconflict_js': return '([\\/a-zA-Z0-9_:\.~-]*)jquery[.-]*no[.-]*[cC]onflict([0-9\.-]|min)*?.js(.*?)';
            case 'migrate_js': return '([\\/a-zA-Z0-9_:\.~-]*)jquery([0-9\.-])*?migrate([0-9\.-]|latest|core|min|pack)*?.js(.*?)';
            case 'popper_js': return '([\\/a-zA-Z0-9_:\.~-]*)popper([0-9\.-]|min)*?js(.*?)';
            case 'bootstrap_js': return '([\\/a-zA-Z0-9_:\.~-]*)bootstrap([0-9\.-]|bundle|min)*?js(.*?)';

            case 'jqueryui_css': return '([\\/a-zA-Z0-9_:\.~-]*)jquery[.-]*ui([0-9\.-]|latest|core|custom|min|pack)*?.css(.*?)';
            case 'bootstrap_css': return '([\\/a-zA-Z0-9_:\.~-]*)bootstrap([a-zA-Z0-9\.-]|min)*?css(.*?)';

            case 'noconflict_declaration': return '[^};\n>]*(jQuery|\$)\.no[cC]onflict\(\s*(true|false|)\s*\);';
            case 'caption_declaration': return '([\s\w();,\':\.-]*)JCaption([\s\w();,\':\.-]*)';
        }

        return $regexp;
    }

    static public function getURL($cdn, $name, $protocole, $version, $extra = '')
    {
        switch ($name) {
            case 'jquery_js':
                if ($cdn == 'google') {
                    return $protocole.'//ajax.googleapis.com/ajax/libs/jquery/'.$version.'/jquery'.$extra.'.js';
                } else if ($cdn == 'cloudflare') {
                    return $protocole.'//cdnjs.cloudflare.com/ajax/libs/jquery/'.$version.'/jquery'.$extra.'.js';
                } else if ($cdn == 'microsoft') {
                    return $protocole.'//ajax.aspnetcdn.com/ajax/jquery/jquery-'.$version.$extra.'.js';
                }
                return $protocole.'//code.jquery.com/jquery-'.$version.$extra.'.js';

            case 'migrate':
                if ($cdn == 'cloudflare') {
                    return $protocole.'//cdnjs.cloudflare.com/ajax/libs/jquery-migrate/'.$version.'/jquery-migrate'.$extra.'.js';
                } else if ($cdn == 'microsoft') {
                    return $protocole.'//ajax.aspnetcdn.com/ajax/jquery.migrate/jquery-migrate-'.$version.$extra.'.js';
                }
                return $protocole.'//code.jquery.com/jquery-migrate-'.$version.$extra.'.js';

            case 'mobile_js':
                if ($cdn == 'cloudflare') {
                    return $protocole.'//cdnjs.cloudflare.com/ajax/libs/jquery-mobile/'.$version.'/jquery.mobile'.$extra.'.js';
                } else if ($cdn == 'microsoft') {
                    return $protocole.'//ajax.aspnetcdn.com/ajax/jquery.mobile/'.$version.'/jquery.mobile-'.$version.$extra.'.js';
                }
                return $protocole.'//code.jquery.com/mobile/'.$version.'/jquery.mobile-'.$version.$extra.'.js';

            case 'mobile_default_css':
                if ($cdn == 'cloudflare') {
                    return $protocole.'//cdnjs.cloudflare.com/ajax/libs/jquery-mobile/'.$version.'/jquery.mobile'.$extra.'.css';
                } else if ($cdn == 'microsoft') {
                    return $protocole.'//ajax.aspnetcdn.com/ajax/jquery.mobile/'.$version.'/jquery.mobile-'.$version.$extra.'.css';
                }
                return $protocole.'//code.jquery.com/mobile/'.$version.'/jquery.mobile-'.$version.$extra.'.css';

            case 'mobile_css':
                if ($cdn == 'cloudflare') {
                    return $protocole.'//cdnjs.cloudflare.com/ajax/libs/jquery-mobile/'.$version.'/jquery.mobile.structure'.$extra.'.css';
                } else if ($cdn == 'microsoft') {
                    return $protocole.'//ajax.aspnetcdn.com/ajax/jquery.mobile/'.$version.'/jquery.mobile.structure-'.$version.$extra.'.css';
                }
                return $protocole.'//code.jquery.com/mobile/'.$version.'/jquery.mobile.structure-'.$version.$extra.'.css';

            case 'jqueryui_js':
                if ($cdn == 'google') {
                    return $protocole.'//ajax.googleapis.com/ajax/libs/jqueryui/'.$version.'/jquery-ui'.$extra.'.js';
                } else if ($cdn == 'cloudflare') {
                    return $protocole.'//cdnjs.cloudflare.com/ajax/libs/jqueryui/'.$version.'/jquery-ui'.$extra.'.js';
                } else if ($cdn == 'microsoft') {
                    return $protocole.'//ajax.aspnetcdn.com/ajax/jquery.ui/'.$version.'/jquery-ui'.$extra.'.js';
                }
                return $protocole.'//code.jquery.com/ui/'.$version.'/jquery-ui'.$extra.'.js';

            case 'jqueryui_css':
                if ($cdn == 'google') {
                    return $protocole.'//ajax.googleapis.com/ajax/libs/jqueryui/'.$version.'/themes/'.$extra.'/jquery-ui.css';
                } else if ($cdn == 'cloudflare') {
                    return $protocole.'//cdnjs.cloudflare.com/ajax/libs/jqueryui/'.$version.'/themes/'.$extra.'/jquery-ui.css';
                } else if ($cdn == 'microsoft') {
                    return $protocole.'//ajax.aspnetcdn.com/ajax/jquery.ui/'.$version.'/themes/'.$extra.'/jquery-ui.css';
                }
                return $protocole.'//code.jquery.com/ui/'.$version.'/themes/'.$extra.'/jquery-ui.css';

            case 'bootstrap_js':
                if ($cdn == 'microsoft') {
                    return $protocole.'//ajax.aspnetcdn.com/ajax/bootstrap/'.$version.'/bootstrap'.$extra.'.js';
                }
                return $protocole.'//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/'.$version.'/js/bootstrap'.$extra.'.js';

            case 'bootstrap_css':
                if ($cdn == 'microsoft') {
                    return $protocole.'//ajax.aspnetcdn.com/ajax/bootstrap/'.$version.'/css/bootstrap'.$extra.'.css';
                }
                return $protocole.'//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/'.$version.'/css/bootstrap'.$extra.'.css';

            case 'bootstrap_responsive_css':
                if ($cdn == 'microsoft') {
                    return $protocole.'//ajax.aspnetcdn.com/ajax/bootstrap/'.$version.'/css/bootstrap-responsive'.$extra.'.css';
                }
                return $protocole.'//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/'.$version.'/css/bootstrap-responsive'.$extra.'.css';

            case 'bootstrap_theme_css':
                if ($cdn == 'microsoft') {
                    return $protocole.'//ajax.aspnetcdn.com/ajax/bootstrap/'.$version.'/css/bootstrap-theme'.$extra.'.css';
                }
                return $protocole.'//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/'.$version.'/css/bootstrap-theme'.$extra.'.css';

            case 'bootstrap_grid_css':
                if ($cdn == 'microsoft') {
                    return $protocole.'//ajax.aspnetcdn.com/ajax/bootstrap/'.$version.'/css/bootstrap-grid'.$extra.'.css';
                }
                return $protocole.'//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/'.$version.'/css/bootstrap-grid'.$extra.'.css';

            case 'bootstrap_reboot_css':
                if ($cdn == 'microsoft') {
                    return $protocole.'//ajax.aspnetcdn.com/ajax/bootstrap/'.$version.'/css/bootstrap-reboot'.$extra.'.css';
                }
                return $protocole.'//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/'.$version.'/css/bootstrap-reboot'.$extra.'.css';

            case 'bootstrap_utilities_css':
            	if ($cdn == 'microsoft') {
            		return $protocole.'//ajax.aspnetcdn.com/ajax/bootstrap/'.$version.'/css/bootstrap-utilities'.$extra.'.css';
        		}
            	return $protocole.'//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/'.$version.'/css/bootstrap-utilities'.$extra.'.css';
        }

        return '';
    }

    static public function addScript($url, $versioning = false, $type = 'text/javascript', $defer = false, $async = false)
    {
        if ($versioning) {
            JFactory::getDocument()->addScriptVersion($url, null, $type, $defer, $async);
        } else {
            JFactory::getDocument()->addScript($url, $type, $defer, $async);
        }
    }

    static public function addScriptDeclaration($declaration, $placeholder = '')
    {
        if ($declaration) {
            if ($placeholder) {
                JFactory::getDocument()->addScriptDeclaration($placeholder);
            } else {
                JFactory::getDocument()->addScriptDeclaration($declaration);
            }
        }
    }

    static public function addStyleSheet($url, $versioning = false, $type = 'text/css', $media = null, $attribs = array())
    {
        if ($versioning) {
            JFactory::getDocument()->addStyleSheetVersion($url, null, $type, $media, $attribs);
        } else {
            JFactory::getDocument()->addStyleSheet($url, $type, $media, $attribs);
        }
    }

    static public function addStyleDeclaration($declaration, $placeholder = '')
    {
        if ($declaration) {
            if ($placeholder) {
                JFactory::getDocument()->addStyleDeclaration($placeholder);
            } else {
                JFactory::getDocument()->addStyleDeclaration($declaration);
            }
        }
    }

    static public function getAdditionalScripts($script_param)
    {
        $script_paths = array();

        $js = trim((string) $script_param);
        if ($js) {
            return array_map('trim', (array) explode("\n", $js));
        }

        return $script_paths;
    }

    static public function prepare_supplement_scripts($supplement_scripts, $versioning = false, $add_placeholder = false)
    {
        $script_paths = array();

        foreach($supplement_scripts as $i => $supplement_script) {

            $script_paths[] = $supplement_script;

            if ($add_placeholder) {

                if (strpos($supplement_script, 'http') !== 0) {
                    $supplement_script = JURI::root().ltrim($supplement_script, '/');
                }

                $useversion = $versioning;
                if (!JUri::isInternal($supplement_script)) {
                    $useversion = false;
                }

                self::addScript($i.'ADD_SCRIPT_HERE', $useversion);
            }
        }

        return $script_paths;
    }

    static public function getAdditionalStylesheets($style_param)
    {
        $stylesheet_paths = array();

        $css = trim((string) $style_param);
        if ($css) {
            return array_map('trim', (array) explode("\n", $css));
        }

        return $stylesheet_paths;
    }

    static public function prepare_supplement_stylesheets($supplement_stylesheets, $versioning = false, $add_placeholder = false)
    {
        $stylesheet_paths = array();

        foreach($supplement_stylesheets as $i => $supplement_stylesheet) {

            $stylesheet_paths[] = $supplement_stylesheet;

            if ($add_placeholder) {

                if (strpos($supplement_stylesheet, 'http') !== 0) {
                    $supplement_stylesheet = JURI::root().ltrim($supplement_stylesheet, '/');
                }

                $useversion = $versioning;
                if (!JUri::isInternal($supplement_stylesheet)) {
                    $useversion = false;
                }

                self::addStyleSheet($i.'ADD_STYLESHEET_HERE', $useversion);
            }
        }

        return $stylesheet_paths;
    }

    static public function paths_are_identical($url, $path, $use_backward_compatibility = false)
    {
        $first_pos = (strpos($path, '*') === 0) ? true: false;
        $last_pos = (strrpos($path, '*') === (strlen($path) - 1)) ? true: false;

        if (JFactory::getConfig()->get('unicodeslugs') == 1) {
            $url = urldecode($url);
        }

        if (($first_pos && $last_pos && !$use_backward_compatibility) || ($first_pos && $use_backward_compatibility)) { // any URL containing $path
            $path = trim($path, '*');
            if (stripos($url, $path) !== false) {
                return true;
            }
        } else if ($first_pos && !$last_pos && !$use_backward_compatibility) { // any URL ending with $path
            $path = ltrim($path, '*');
            $path_length = strlen($path);
            $url_tip = substr($url, -$path_length);
            if (strcasecmp($url_tip, $path) == 0) { // compare end of URI with $path
                return true;
            }
        } else if (!$first_pos && $last_pos && !$use_backward_compatibility) { // any URL starting with $path
            $path = rtrim($path, '*');
            $url = str_replace('index.php/', '', $url);
            $path = str_replace('index.php/', '', $path);
            if (stripos($url, JURI::root().ltrim($path, '/')) !== false) {
                return true;
            }
        } else {
            $url = str_replace('index.php/', '', $url);
            $path = str_replace('index.php/', '', $path);
            if (strcasecmp($url, JURI::root().ltrim($path, '/')) == 0) { // case-insensitive string comparison
                return true;
            }
        }

        return false;
    }

    /**
     * search through array of strings BUT remove the only part that matches, not the whole string
     *
     * @param string $regexp
     * @param array|string $container
     * @param string $replace
     */
    static public function search_and_replace($regexp, &$container, $replace = '', $limit = -1)
    {
        $total_count = 0;
        
        if (is_array($container)) {
            foreach ($container as $key => $value) {
                $value = preg_replace('/' . $regexp . '/', $replace, $value, $limit, $count);
                $total_count += $count;
                if (trim($value) == '') {
                    unset($container[$key]);
                    continue;
                }
                $container[$key] = $value;
            }
        } else {
            $container = preg_replace('#' . $regexp . '#', $replace, $container, $limit, $count);
            $total_count += $count;
        }
        
        return $total_count;
    }

    /**
     * specific search for noconflict code
     *
     * @param string $regexp
     * @param array|string $container
     * @param boolean $keep_var
     * @param array $verbose
     */
    static public function search_and_replace_noconflict($regexp, &$container, $keep_var, &$verbose, $remove_script_tags = false)
    {
        if (is_array($container)) {
            foreach ($container as $key => $value) {
                
                $matches = array();
                if (preg_match_all('/' . $regexp . '/', $value, $matches, PREG_SET_ORDER) > 0) {
                    foreach ($matches as $match) {
                        $quoted_match = preg_quote($match[0]); // prepares for regexp
                        if (!$keep_var) { // variable declarations included
                            $value = preg_replace('/' . $quoted_match . '/', '', $value, 1);
                            self::report($verbose, 'deleted', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_REMOVEDNOCONFLICTSCRIPTDECLARATIONS', $match[0]);
                        } else { // ignore the removal of variable declaration (keep var|let|const j = $.noConflict(); BUT replace $)
                            if (preg_match('/(.*)=/i', $match[0])) {
                                if (strpos($match[0], '$') !== false) {
                                    $match[0] = str_replace('$.', 'jQuery.', $match[0]);
                                    $value = preg_replace('/' . $quoted_match . '/', $match[0], $value, 1);
                                    self::report($verbose, 'info', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_KEPTANDFIXEDNOCONFLICTSCRIPTDECLARATION', $match[0]);
                                } else {
                                    self::report($verbose, 'info', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_KEPTNOCONFLICTSCRIPTDECLARATION', $match[0]);
                                }
                            } else {
                                $value = preg_replace('/' . $quoted_match . '/', '', $value, 1);
                                self::report($verbose, 'deleted', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_REMOVEDNOCONFLICTSCRIPTDECLARATIONS', $match[0]);
                            }
                        }
                    }
                }
                
                if (trim($value) == '') {
                    unset($container[$key]);
                    continue;
                }
                $container[$key] = $value;
            }
        } else {
            $matches = array();
            if (preg_match_all('#'.$regexp.'#', $container, $matches, PREG_SET_ORDER) > 0) {
                
                $number_of_deletions = 0;
                
                foreach ($matches as $match) {
                    $quoted_match = preg_quote($match[0], '#'); // prepares for regexp
                    if (!$keep_var) { // variable declarations included
                        $container = preg_replace('#'.$quoted_match.'#', '', $container, 1);
                        self::report($verbose, 'deleted', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_REMOVEDNOCONFLICTSCRIPTDECLARATIONS', $match[0]);
                        $number_of_deletions++;
                    } else { // ignore the removal if variable declaration (keep var|let|const j = $.noConflict(); BUT replace $)
                        if (preg_match('/(.*)=/i', $match[0])) {
                            if (strpos($match[0], '$') !== false) {
                                $match[0] = str_replace('$.', 'jQuery.', $match[0]);
                                $container = preg_replace('#' . $quoted_match . '#', $match[0], $container, 1);
                                self::report($verbose, 'info', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_KEPTANDFIXEDNOCONFLICTSCRIPTDECLARATION', $match[0]);
                            } else {
                                self::report($verbose, 'info', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_KEPTNOCONFLICTSCRIPTDECLARATION', $match[0]);
                            }
                        } else {
                            $container = preg_replace('#' . $quoted_match . '#', '', $container, 1);
                            self::report($verbose, 'deleted', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_REMOVEDNOCONFLICTSCRIPTDECLARATIONS', $match[0]);
                            $number_of_deletions++;
                        }
                    }
                }
                
                // TODO make sure javascript does not need to be quoted
                if ($remove_script_tags && $number_of_deletions > 0) {
                    $count = 0;
                    $container = preg_replace('#<script type="text/javascript">[\s]*?</script>#', '', $container, -1, $count); // remove newly empty scripts, if any
                    if ($count > 0) {
                        self::report($verbose, 'deleted', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_REMOVEDEMPTYSCRIPTTAGS', $count);
                    }
                }
            }
        }
    }

    /**
     *  Remove all occurences of a script or a stylesheet
     *  returns
     *  - an array of removed items if requesting results
     *  - the removal count otherwise
     **/
    static public function search_and_delete($type, $regexp, &$container, &$verbose = null, $ignore_files = array(), $request_results = false)
    {
        $removed = array();
        $num_removed = 0;

        if (is_array($container)) {

            $results = preg_grep('/' . $regexp . '/', array_keys($container));

            if (!empty($results)) {
                foreach ($results as $result) {
                    if (!empty($ignore_files)) {
                        $ignore = false;
                        foreach ($ignore_files as $ignore_file) {
                            if (stripos($result, $ignore_file) !== false) { // library needs to be ignored from removal
                                $ignore = true;
                                if (!is_null($verbose)) {
                                    $verbose[] = array('info', JText::sprintf('PLG_SYSTEM_JQUERYEASY_VERBOSE_IGNORE' . ($type == 'js' ? 'SCRIPT' : 'STYLESHEET'), $ignore_file));
                                }
                                break;
                            }
                        }
                        if (!$ignore) {
                            unset($container[$result]);
                            $num_removed++;
                            $removed[] = $result;
                        }
                    } else {
                        unset($container[$result]);
                        $num_removed++;
                        $removed[] = $result;
                    }
                }
            }

        } else {

            $regexp = ($type == 'js' ? 'src="' : 'href="') . $regexp . '"';

            if (empty($ignore_files) && !$request_results) {
                $container = preg_replace('#'.$regexp.'#', 'GARBAGE', $container, -1, $num_removed);
            } else {
                $matches = array();
                if (preg_match_all('#'.$regexp.'#', $container, $matches, PREG_SET_ORDER) >= 0) {
                    foreach ($matches as $match) {
                        $quoted_match = preg_quote($match[0], '/'); // prepares for regexp
                        $ignore = false;
                        foreach ($ignore_files as $ignore_file) {
                            if (stripos($match[0], $ignore_file) !== false) { // library needs to be ignored for removal
                                $ignore = true;
                                if (!is_null($verbose)) {
                                    $verbose[] = array('info', JText::sprintf('PLG_SYSTEM_JQUERYEASY_VERBOSE_IGNORE' . ($type == 'js' ? 'SCRIPT' : 'STYLESHEET'), $ignore_file));
                                }
                                break;
                            }
                        }
                        if (!$ignore) { // remove the library
                            $container = preg_replace('#'.$quoted_match.'#', 'GARBAGE', $container, 1);
                            $num_removed++;
                            $removed[] = ($type == 'js') ? rtrim(substr($match[0], 5), '"') : rtrim(substr($match[0], 6), '"');
                        }
                    }
                }
            }
        }

        if ($request_results) {
            return $removed;
        }

        return $num_removed;
    }

    static public function search_and_report($type, $regexp, &$container, &$verbose = null)
    {
    	$found = array();
    	$num_found = 0;

    	if (is_array($container)) {

    		$results = preg_grep('/' . $regexp . '/', array_keys($container));

    		if (!empty($results)) {
    			foreach ($results as $result) {
    				$num_found++;
    				$found[] = $result;
    			}
    		}

    	} else {

    		$regexp = ($type == 'js' ? 'src="' : 'href="') . $regexp . '"';

    		$matches = array();
    		if (preg_match_all('#'.$regexp.'#', $container, $matches, PREG_SET_ORDER) >= 0) {
    			foreach ($matches as $match) {
    				$num_found++;
    				$found[] = ($type == 'js') ? rtrim(substr($match[0], 5), '"') : rtrim(substr($match[0], 6), '"');
    			}
    		}
    	}

    	return array($num_found, $found);
    }

    static public function report(&$verbose, $type, $message, $parameter_1 = null, $parameter_2 = null)
    {
        if (!is_null($verbose)) {
            if (isset($parameter_1) && isset($parameter_2)) {
                $verbose[] = array($type, JText::sprintf($message, $parameter_1, $parameter_2));
            } else if (isset($parameter_1) || isset($parameter_2)) {
                $parameter = $parameter_1 ? $parameter_1 : $parameter_2;
                $verbose[] = array($type, JText::sprintf($message, $parameter));
            } else {
                $verbose[] = array($type, JText::_($message));
            }
        }
    }

    static public function getReport($comments = array(), $execution_time = 0, $title = '', $as_modal = true)
    {
        $replacement = array();

        $replacement[] = '<style type="text/css"> ';

        if ($as_modal) {
            $replacement[] = '#jqe_report_overlay { z-index: 9000; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(130, 130, 130, 0.6); } ';
            $replacement[] = '#jqe_report_min { z-index: 10000; display: none; overflow: hidden; position: fixed; top: 10px; right: 10px; padding: 10px; font-family: Arial, sans-serif; font-size: 12px; } ';
            $replacement[] = '#jqe_report { z-index: 10000; display: block; overflow: hidden; position: fixed; top: 10px; left: 0; right: 0; width: 90%; max-width: 1000px; margin: 0 auto; padding: 10px 20px 30px 20px; box-sizing: border-box; font-family: Arial, sans-serif; font-size: 12px; } ';
        } else {
            $replacement[] = '#jqe_report { clear: both; overflow: hidden; width: 100%; padding: 10px 20px 30px 20px; box-sizing: border-box; font-family: Arial, sans-serif; font-size: 12px; } ';
        }

        $replacement[] = '#jqe_report > div { position: relative; overflow: hidden; width: 100%; margin: 0 auto; border-radius: 4px; box-shadow: 0 12px 15px 0 rgba(0, 0, 0, 0.25); background: #fff; } ';
        $replacement[] = '#jqe_report code { white-space: normal; word-break: break-all; font-size: 1em; } ';
        $replacement[] = '#jqe_report .jqe_header, #jqe_report .jqe_footer { position: relative; overflow: hidden; width: 100%; padding: 10px 15px; box-sizing: border-box; background-color: #eee; } ';

        if ($title) {
            $replacement[] = '#jqe_report .jqe_header h3 > em { color: #d14; padding: 0 2px; } ';
        }

        $replacement[] = '#jqe_report .jqe_footer > span { line-height: 36px; } ';

        if ($as_modal) {
            $replacement[] = '#jqe_report .jqe_footer > button, #jqe_report_min button { width: auto; float: right; font-size: 12px; padding: 5px 10px; border: none; background-color: #4e4e4e; color: #fff; font-weight: bold; } ';
            $replacement[] = '#jqe_report .jqe_footer > button:hover, #jqe_report_min button:hover { background-color: #000; } ';
            $replacement[] = '#jqe_report .jqe_content { padding: 0; margin: 15px; overflow: auto; max-height: 200px; max-height: 60vh } ';
        } else {
            $replacement[] = '#jqe_report .jqe_content { padding: 0; margin: 15px; overflow: auto; } ';
        }

        $replacement[] = '</style>'.chr(13);

        if ($as_modal) {
            $replacement[] = '<div id="jqe_report_min">';
            $replacement[] = '<button onclick="document.getElementById(\'jqe_report_min\').style.display = \'none\'; document.getElementById(\'jqe_report\').style.display = \'block\'; document.getElementById(\'jqe_report_overlay\').style.display = \'block\'; return false;">'.JText::_('JSHOW').'</button>';
            $replacement[] = '</div>';

            $replacement[] = '<div id="jqe_report_overlay"></div>';
        }

        $replacement[] = '<div id="jqe_report">';
        $replacement[] = '<div>';

        // header

        $replacement[] = '<div class="jqe_header">';
        $replacement[] = '<h2>'.JText::_('PLG_SYSTEM_JQUERYEASY_VERBOSE_JQUERYEASY').'</h2>';
        if ($title) {
            $replacement[] = '<h3>'.$title.'</h3>';
        }
        $replacement[] = '</div>';

        // content

        $replacement[] = '<dl class="jqe_content">';
        $replacement[] = '<dt style="position: absolute; top: -9999px; left: -9999px;">'.JText::_('PLG_SYSTEM_JQUERYEASY_VERBOSE_JQUERYEASY').'</dt>';

        if (!empty($comments)) {
            foreach ($comments as $comment) {

                switch ($comment[0]) {
                    case 'info': $color = '#0c5460'; $bgcolor = '#d1ecf1'; $label = '<span class="label" style="display: inline-block; background-color: '.$bgcolor.'; width: 15px; margin: 1px 5px 1px 0;">&nbsp;</span>'; break;
                    case 'deleted': $color = '#856404'; $bgcolor = '#fff3cd'; $label = '<span class="label" style="display: inline-block; background-color: '.$bgcolor.'; width: 15px; margin: 1px 5px 1px 0;">&nbsp;</span>'; break;
                    case 'error': $color = '#721c24'; $bgcolor = '#f8d7da'; $label = '<span class="label" style="display: inline-block; background-color: '.$bgcolor.'; width: 15px; margin: 1px 5px 1px 0;">&nbsp;</span>'; break;
                    case 'added': $color = '#155724'; $bgcolor = '#d4edda'; $label = '<span class="label" style="display: inline-block; background-color: '.$bgcolor.'; width: 15px; margin: 1px 5px 1px 0;">&nbsp;</span>'; break;
                    default: $color = '#1b1e21'; $bgcolor = '#d6d8d9'; $label = '<span class="label" style="display: inline-block; background-color: '.$bgcolor.'; width: 15px; margin: 1px 5px 1px 0;">&nbsp;</span>';
                }

                $replacement[] = '<dd style="color: '.$color.'; margin-bottom: 6px;">'.$label.$comment[1].'</dd>';
            }
        } else {
            $replacement[] = '<dd>'.JText::_('PLG_SYSTEM_JQUERYEASY_VERBOSE_NOCHANGESMADE').'</dd>';
        }

        $replacement[] = '</dl>';

        // footer

        $replacement[] = '<div class="jqe_footer">';
        $replacement[] = '<span>'.JText::_('PLG_SYSTEM_JQUERYEASY_VERBOSE_EXECUTIONTIME').': '.number_format($execution_time, 4).'</span>';

        if ($as_modal) {
            $replacement[] = '<button onclick="document.getElementById(\'jqe_report_min\').style.display = \'block\'; document.getElementById(\'jqe_report\').style.display = \'none\'; document.getElementById(\'jqe_report_overlay\').style.display = \'none\'; return false;">'.JText::_('JHIDE').'</button>';
        }

        $replacement[] = '</div>';

        // end

        $replacement[] = '</div>';
        $replacement[] = '</div>';

        return implode('', $replacement).chr(13);
    }
    
    //$root_path = (strpos($this->_jqpath, 'http') !== 0) ? $this->_root . Uri::root(true) . '/' . $this->_jqpath : $this->_jqpath;
    //$new_scripts[$this->_jqpath] = array('type' => 'text/javascript', 'options' => (Uri::isInternal($root_path) ? ['version' => $version->getMediaVersion()] : array()));
    
    //var_dump($this->_root); // http://localhost:7878
    //var_dump(Uri::root(true)); // /MyWork_4_x_free_test
    //var_dump(JPATH_ROOT); // E:\wamp64\www\MyWork_4_x_free_test
    
    //var_dump($root_path); // http://localhost:7878/MyWork_4_x_free_test/media/vendor/jquery/js/jquery.js
    //var_dump(Uri::isInternal($root_path)); // true
    
    static public function isInternal($path)
    {
        $root = str_replace(JUri::root(true) . '/', '', JUri::root());
        
        $root_path = (strpos($path, 'http') !== 0) ? $root . JUri::root(true) . '/' . $path : $path;
        
        if (JUri::isInternal($root_path)) {
            return true;
        }
        
        return false;
    }

    static public function getJQueryPath($protocole, $compressed, $params, &$verbose, $cdn= 'google', $suffix = '')
    {
        $jQueryVersion = $params->get('jqueryversion'.$suffix, '1.8');

        if ($jQueryVersion == 'joomla') {
            return JURI::root(true).'/media/jui/js/jquery'.$compressed.'.js';
        } else {
            if ($jQueryVersion == 'local') {
                $localVersionPath = trim($params->get('localversion'.$suffix, ''));
                if ($localVersionPath) {
                    if (JFile::exists(JPATH_ROOT.$localVersionPath)) {
                        return JURI::root(true).$localVersionPath;
                    } else {
                        self::report($verbose, 'error', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_COULDNOTFINDFILE', JPATH_ROOT.$localVersionPath);
                    }
                } else {
                    self::report($verbose, 'error', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_EMPTYLOCALFILE', 'jQuery');
                }
            } else {

                $jQuerySubversion = trim($params->get('jquerysubversion'.$suffix, ''));

                $values_that_do_not_need_subversion = array('1.3', '1.4', '1.5', '1.6', '1.7', '1.8');
                if ($jQuerySubversion == '' && !in_array($jQueryVersion, $values_that_do_not_need_subversion)) {
                    $jQuerySubversion = '0';
                }

                if ($jQuerySubversion != '') {
                    $jQuerySubversion = '.'.$jQuerySubversion;
                }

                return self::getURL($cdn, 'jquery_js', $protocole, $jQueryVersion.$jQuerySubversion, $compressed);
            }
        }

        return '';
    }

    static public function getMigratePath($protocole, $compressed, $params, &$verbose, $cdn= 'google', $suffix = '')
    {
        $jQueryVersion = $params->get('jqueryversion'.$suffix, '1.8');
        $migrateVersion = $params->get('migrateversion'.$suffix, 'none');

        if ($migrateVersion != 'none') {

            $migrate_is_unnecessary = false;

            if ($jQueryVersion == 'joomla') {
                if (version_compare(JVERSION, '3.2', 'lt')) {
                    $migrate_is_unnecessary = true;
                }
            } else if ($jQueryVersion == '1.3' || $jQueryVersion == '1.4' || $jQueryVersion == '1.5' || $jQueryVersion == '1.6' || $jQueryVersion == '1.7' || $jQueryVersion == '1.8') {
                $migrate_is_unnecessary = true;
            }

            if (!$migrate_is_unnecessary) {
                if ($migrateVersion == 'joomla') {
                    return JURI::root(true).'/media/jui/js/jquery-migrate'.$compressed.'.js';
                } else {
                    if ($migrateVersion == 'local') {
                        $localPathMigrate = trim($params->get('localpathmigrate'.$suffix, ''));
                        if ($localPathMigrate) {
                            if (JFile::exists(JPATH_ROOT.$localPathMigrate)) {
                                return JURI::root(true).$localPathMigrate;
                            } else {
                                self::report($verbose, 'error', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_COULDNOTFINDFILE', JPATH_ROOT.$localPathMigrate);
                            }
                        } else {
                            self::report($verbose, 'error', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_EMPTYLOCALFILE', 'Migrate');
                        }
                    } else {

                        if ($migrateVersion == '3.0.0') { // for backward compatibility
                            $migrateVersion = '3.0';
                        }

                        $migrateSubversion = trim($params->get('migratesubversion'.$suffix, ''));

                        $values_that_do_not_need_subversion = array('1.2.1', '1.3.0', '1.4.1');

                        if (in_array($migrateVersion, $values_that_do_not_need_subversion)) {
                            $migrateSubversion = '';
                        } else if ($migrateSubversion == '') { // missing sub-version
                            $migrateSubversion = '0';
                        }

                        if ($migrateSubversion != '') {
                            $migrateSubversion = '.'.$migrateSubversion;
                        }

                        return self::getURL($cdn, 'migrate', $protocole, $migrateVersion.$migrateSubversion, $compressed);
                    }
                }
            } else {
                self::report($verbose, 'error', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_MIGRATEUNNECESSARY');
            }
        }

        return '';
    }

    static public function getjQueryUIPath($protocole, $compressed, $params, &$verbose, $cdn= 'google', $suffix = '')
    {
        $jQueryUIVersion = $params->get('jqueryuiversion'.$suffix, '1.9');

        if ($jQueryUIVersion == 'joomla') {
            return JURI::root(true).'/media/jui/js/jquery.ui.core'.$compressed.'.js';
        } else {
            if ($jQueryUIVersion == 'local') {
                $localVersionPath = trim($params->get('localuiversion'.$suffix, ''));
                if ($localVersionPath) {
                    if (JFile::exists(JPATH_ROOT.$localVersionPath)) {
                        return JURI::root(true).$localVersionPath;
                    } else {
                        self::report($verbose, 'error', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_COULDNOTFINDFILE', JPATH_ROOT.$localVersionPath);
                    }
                } else {
                    self::report($verbose, 'error', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_EMPTYLOCALFILE', 'jQuery UI');
                }
            } else {
                $jQueryUISubversion = trim($params->get('jqueryuisubversion'.$suffix, ''));

                $values_that_do_not_need_subversion = array('1.7', '1.8');
                if ($jQueryUISubversion == '' && !in_array($jQueryUIVersion, $values_that_do_not_need_subversion)) {
                    $jQueryUISubversion = '0';
                }

                if ($jQueryUISubversion != '') {
                    $jQueryUISubversion = '.'.$jQueryUISubversion;
                }

                return self::getURL($cdn, 'jqueryui_js', $protocole, $jQueryUIVersion.$jQueryUISubversion, $compressed);
            }
        }

        return '';
    }

    static public function getjQueryUICSSPath($protocole, $compressed, $params, &$verbose, $cdn= 'google', $suffix = '')
    {
        $jQueryUITheme = $params->get('jqueryuitheme'.$suffix, 'none');

        if ($jQueryUITheme != 'none') {

            $jQueryUIVersion = $params->get('jqueryuiversion'.$suffix, '1.9');

            if ($jQueryUITheme == 'custom' || $jQueryUIVersion == 'joomla' || $jQueryUIVersion == 'local') {
                $localVersionPath = trim($params->get('jqueryuithemecustom'.$suffix, ''));
                if ($localVersionPath) {
                    if (JFile::exists(JPATH_ROOT.$localVersionPath)) {
                        return JURI::root(true).$localVersionPath;
                    } else {
                        self::report($verbose, 'error', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_COULDNOTFINDFILE', JPATH_ROOT.$localVersionPath);
                    }
                } else {
                    self::report($verbose, 'error', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_EMPTYLOCALCSSFILE');
                }
            } else {
                $jQueryUISubversion = trim($params->get('jqueryuisubversion'.$suffix, ''));

                $values_that_do_not_need_subversion = array('1.7', '1.8');
                if ($jQueryUISubversion == '' && !in_array($jQueryUIVersion, $values_that_do_not_need_subversion)) {
                    $jQueryUISubversion = '0';
                }

                if ($jQueryUISubversion != '') {
                    $jQueryUISubversion = '.'.$jQueryUISubversion;
                }

                return self::getURL($cdn, 'jqueryui_css', $protocole, $jQueryUIVersion.$jQueryUISubversion, $jQueryUITheme);
            }
        }

        return '';
    }

}
