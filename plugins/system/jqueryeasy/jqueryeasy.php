<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

defined( '_JEXEC' ) or die;

jimport('joomla.plugin.plugin');
jimport('joomla.filesystem.file');

require_once (dirname(__FILE__).'/helper.php');

class plgSystemJqueryeasy extends JPlugin
{
    protected $autoloadLanguage = true;

	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
		
		$this->_enabled = false;

		if (JFactory::getApplication()->isSite()) {

    		$this->loadLanguage();

    		$this->_versioning = $this->params->get('versioning', false);
    		if (version_compare(JVERSION, '3.2', 'lt')) {
    			$this->_versioning = false;
    		}

    		$this->_version = null;
    		if ($this->_versioning) {
    			$this->_version = JFactory::getDocument()->getMediaVersion();
    		}
    		
    		$this->_apionly = false;

    		$this->_cdn = 'google';

    		$this->_showreport = false;
    		$this->_verbose_array = null;

    		$this->_supplement_scripts = array();
    		$this->_supplement_stylesheets = array();

    		$this->_usejQuery = false;
    		$this->_usejQueryUI = false;

    		$this->_jqpath = '';
    		$this->_jqmigratepath = '';
    		$this->_jqnoconflictpath = '';

    		$this->_jquipath = '';
    		$this->_jquicsspath = '';

    		$this->_timeafterroute = 0;
    		$this->_timebeforerender = 0;
    		$this->_timebeforecompilehead = 0;
    		$this->_timeafterrender = 0;
    		
    		$this->_suffix = 'frontend';
		}
	}

	function onAfterRoute()
	{
	    if (!JFactory::getApplication()->isSite()) {
	        return;
	    }
	    
		$this->_enabled = plgJQueryEasyHelper::isEnabledOnPage($this->params); // parameters checked have no suffix

		if (!$this->_enabled) {
		    return;
		}
		
		// report
		
		$showreport = $this->params->get('showreport', 0);
		
		if ($showreport == 1 || $showreport == 3) {
		    $this->_showreport = true;
		} else if ($showreport == 2 || $showreport == 4) { // only show report when Super User is logged in
		    $this->_showreport = JFactory::getUser()->authorise('core.admin') ? true : false;
		}
		
		if ($this->_showreport) {
		    $this->_verbose_array = array();
		}
		
		// page scan
		
		$pagescan = (int)$this->params->get('pagescan', 0);
		
		if ($pagescan === 0) {
		    $this->_apionly = true;
		    plgJQueryEasyHelper::report($this->_verbose_array, 'message', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_MODIFICATIONSAPIONLY');
		} elseif ($pagescan === 1) {
		    plgJQueryEasyHelper::report($this->_verbose_array, 'message', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_MODIFICATIONSHEADONLY');
		} else {
		    plgJQueryEasyHelper::report($this->_verbose_array, 'message', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_PROCESSINGTHEWHOLEDOCUMENT');
		}
		
		// protocole
		
		$protocole = $this->params->get('whichhttp' . $this->_suffix, 'https');
		$protocole = ($protocole == 'none') ? '' : $protocole.':';
		
		// compression
		
		$compressed = '';
		if ($this->params->get('compression' . $this->_suffix, 'compressed') == 'compressed' && !(defined('JDEBUG') && JDEBUG)) {
		    $compressed = '.min';
		}

        // timing starts
        
		$time_start = microtime(true);

		// BEGIN prepare spaces to fill with script

		$javascript = plgJQueryEasyHelper::getAdditionalScripts($this->params->get('addjavascript' . $this->_suffix, ''));
		if (!empty($javascript)) {
		    $this->_supplement_scripts = plgJQueryEasyHelper::prepare_supplement_scripts(array_unique($javascript), $this->_versioning, ($this->_apionly ? false : true));
		}

		// END prepare spaces to fill with scripts

		// BEGIN prepare spaces to fill with scripts declarations

		if (!$this->_apionly) {
			plgJQueryEasyHelper::addScriptDeclaration(trim((string) $this->params->get('addjavascriptdeclaration' . $this->_suffix, '')), 'ADD_SCRIPT_DECLARATION_HERE');
		}

		// END prepare spaces to fill with scripts declarations

		// BEGIN prepare spaces to fill with stylesheets and stylesheets declarations

		$css = plgJQueryEasyHelper::getAdditionalStylesheets($this->params->get('addcss' . $this->_suffix, ''));
		if (!empty($css)) {
		    $this->_supplement_stylesheets = plgJQueryEasyHelper::prepare_supplement_stylesheets(array_unique($css), $this->_versioning, ($this->_apionly ? false : true));
		}

		if (!$this->_apionly) {
		    plgJQueryEasyHelper::addStyleDeclaration(trim((string) $this->params->get('addcssdeclaration' . $this->_suffix, '')), 'ADD_STYLESHEET_DECLARATION_HERE');
		}

		// END prepare spaces to fill with stylesheets and stylesheets declarations

		// jQuery

		switch ($this->params->get('jqueryin' . $this->_suffix, 0)) {
			case 1: $this->_usejQuery = true; break;
			case 2: $this->_usejQuery = true; $this->_usejQueryUI = true; break;
			default: break;
		}

		if ($this->_usejQuery) {

		    $this->_jqpath = plgJQueryEasyHelper::getJQueryPath($protocole, $compressed, $this->params, $this->_verbose_array, $this->_cdn, $this->_suffix);

		    if (!$this->_apionly && $this->_jqpath) {
		        plgJQueryEasyHelper::addScript('JQEASY_JQLIB', (plgJQueryEasyHelper::isInternal($this->_jqpath) ? $this->_versioning : false));
			}

			// jQuery Migrate

			$this->_jqmigratepath = plgJQueryEasyHelper::getMigratePath($protocole, $compressed, $this->params, $this->_verbose_array, $this->_cdn, $this->_suffix);

			if (!$this->_apionly && $this->_jqmigratepath) {
			    plgJQueryEasyHelper::addScript('JQEASY_JQMIGRATELIB', (plgJQueryEasyHelper::isInternal($this->_jqmigratepath) ? $this->_versioning : false));
			}

			// no conflict path

			$addjQueryNoConflict = $this->params->get('addnoconflict' . $this->_suffix, 2);
			if ($addjQueryNoConflict == 1) {
			    if (!$this->_apionly) {
			        JFactory::getDocument()->addScriptDeclaration('JQEASY_JQNOCONFLICT');
			    }
			} else if ($addjQueryNoConflict == 2) {
			    if ($this->params->get('jqueryversion' . $this->_suffix, '1.8') == 'joomla') {
					$this->_jqnoconflictpath = JUri::root(true).'/media/jui/js/jquery-noconflict.js';
				} else {
					$this->_jqnoconflictpath = JUri::root(true).'/media/syw_jqueryeasy/js/jquerynoconflict.js';
				}

				if (!$this->_apionly) {
				    plgJQueryEasyHelper::addScript('JQEASY_JQNOCONFLICT', $this->_versioning);
				}
			}

			// jQuery UI

			if ($this->_usejQueryUI) {

			    $this->_jquipath = plgJQueryEasyHelper::getjQueryUIPath($protocole, $compressed, $this->params, $this->_verbose_array, $this->_cdn, $this->_suffix);

			    if (!$this->_apionly && $this->_jquipath) {
			        plgJQueryEasyHelper::addScript('JQEASY_JQUILIB', (plgJQueryEasyHelper::isInternal($this->_jquipath) ? $this->_versioning : false));
				}

				$this->_jquicsspath = plgJQueryEasyHelper::getjQueryUICSSPath($protocole, $compressed, $this->params, $this->_verbose_array, $this->_cdn, $this->_suffix);

				if (!$this->_apionly && $this->_jquicsspath) {
				    plgJQueryEasyHelper::addStyleSheet('JQEASY_JQUICSS', (plgJQueryEasyHelper::isInternal($this->_jquicsspath) ? $this->_versioning : false));
				}
			} // END jQuery UI
		} // END jQuery

		$time_end = microtime(true);
		$this->_timeafterroute = $time_end - $time_start;
	}

	function onBeforeCompileHead()
	{
	    if (!$this->_enabled) {
	        return;
	    }

	    // timing starts
	    
	    $time_start = microtime(true);

	    $scripts = JFactory::getDocument()->_scripts;

        //var_dump($scripts);

	    $script_declarations = JFactory::getDocument()->_script; // array of script declarations
	    if (!isset($script_declarations['text/javascript'])) {
	    	$script_declarations['text/javascript'] = '';
	    }

        //var_dump($script_declarations['text/javascript']);

	    $styles = JFactory::getDocument()->_styleSheets;

        //var_dump($styles);

	    $style_declarations = JFactory::getDocument()->_style; // array of style declarations
	    if (!isset($style_declarations['text/css'])) {
	    	$style_declarations['text/css'] = '';
	    }

        //var_dump($style_declarations['text/css']);

        // caption

	    $disable_caption = $this->params->get('disablecaptions', 0);

	    if ($disable_caption) {
	        $number_removed = plgJQueryEasyHelper::search_and_delete('js', preg_quote('media/system/js/caption', '/'), $scripts, $this->_verbose_array);
	        if ($number_removed > 0) {
	            plgJQueryEasyHelper::report($this->_verbose_array, 'deleted', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_REMOVEDCAPTIONLIBRARY');
	        }

	        $regexp = plgJQueryEasyHelper::getRegularExpression('declaration', 'caption');

	        if (version_compare(JVERSION, '3.2.0', 'ge')) {
	            $regexp = '(jQuery|\$)\(window\).on\(\'load\',[\s]*?function\(\)[\s]*?{[\s]*?'.$regexp.'[\s]*?}\);';
	        } else {
	            $regexp = 'window.addEvent\(\'load\', function\(\) {[\s]*?'.$regexp.'[\s]*?}\);'; // MooTools
	        }
	        
	        $number_removed = plgJQueryEasyHelper::search_and_replace($regexp, $script_declarations['text/javascript'], '', 1);
	        
	        if ($number_removed > 0) {
	            plgJQueryEasyHelper::report($this->_verbose_array, 'deleted', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_REMOVECAPTION');
	        }
	    }

	    $disable_mootools = $this->params->get('disablemootools', 0);

	    // get rid of MooTools only if :
	    // + view != form (edit or create forms) - form validation: no way to tell which validation has been called after J!3.4 / keep alive code
	    // + tmpl != component (component.php used to get images from editor for instance)
	    // + not in specified pages
	    // + unnedded by another library loaded from media/system/js

	    if ($disable_mootools) {
	        if (JFactory::getApplication()->input->get('view', '') == 'form' || JFactory::getApplication()->input->get('tmpl', '') == 'component') {
	            $disable_mootools = false;
	        } else { // DO NOT REMOVE if a page has been specifically listed as not to disable MooTools
	            $keep_mootools_in = trim( (string) $this->params->get('keepmootoolsin', ''));
	            if (!empty($keep_mootools_in)) {
	                $paths = array_map('trim', (array) explode("\n", $keep_mootools_in));
	                foreach ($paths as $path) {
	                    if (plgJQueryEasyHelper::paths_are_identical(JUri::current(), $path)) {
	                        $disable_mootools = false;
	                    }
	                }
	            }
	        }
	    }

	    if ($disable_mootools) {
	        $js_needing_mootools = array('mootree'); // when on debug, will remove the uncompressed files

	        if (version_compare(JVERSION, '3.2', 'lt')) {
	            $js_needing_mootools[] = 'multiselect';
	            $js_needing_mootools[] = 'switcher';
	            if (!$disable_caption) { // if caption has not been removed already, useless to add
	                $js_needing_mootools[] = 'caption';
	            }
	        }

	        if (version_compare(JVERSION, '3.4', 'lt')) {
	            $js_needing_mootools[] = 'combobox';
	        }

            if (version_compare(JVERSION, '3.7', 'ge')) {
	        	if (JFactory::getConfig()->get('debug')) { // specific case for modal because modal-fields.js may exist
	            	$js_needing_mootools[] = 'modal-uncompressed.js';
	        	} else {
	            	$js_needing_mootools[] = 'modal.js';
	        	}
            } else {
                $js_needing_mootools[] = 'modal';
            }

	        foreach ($js_needing_mootools as $library) {
	            $results = preg_grep('/' . preg_quote('media/system/js/', '/') . $library . '/', array_keys($scripts));
	            if (!empty($results)) {
	                $disable_mootools = false;
	                break;
	            }
	        }
	    }

        // TODO look for Tips : overkill?
        // do not disable MooTools if found var JTooltips = new Tips($ inside script declarations and if core MooTools tooltips function has not been disabled

	    // remove MooTools

	    if ($disable_mootools) {
	        $js_mootools = array('mootools-core', 'mootools-more'); // when on debug, will remove the uncompressed files
	        foreach ($js_mootools as $library) {
	            $results = preg_grep('/' . preg_quote('media/system/js/', '/') . $library . '/', array_keys($scripts));
	            if (!empty($results)) {
	                foreach ($results as $result) {
	                    unset($scripts[$result]);
	                    plgJQueryEasyHelper::report($this->_verbose_array, 'deleted', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_REMOVEDMOOTOOLSLIBRARY', $result);
	                }
	            }
	        }
	    }

	    $new_scripts = array();
	    $new_styles = array();

	    if ($this->_apionly) {

	        // jQuery

	        $do_not_add_libraries = false;
	        $do_not_add_stylesheets = false;
	        $move_unique_library = false;
	        $move_unique_libraryui = false;
	        $move_unique_cssui = false;

	        if ($this->_usejQuery) {

	            $removejQueryNoConflict = $this->params->get('removenoconflict' . $this->_suffix, 1);
	            if ($removejQueryNoConflict == 1 || $removejQueryNoConflict == 2) {

	                // remove all '...jQuery.noConflict(...);' or '... $.noConflict(...);'

	                plgJQueryEasyHelper::search_and_replace_noconflict(plgJQueryEasyHelper::getRegularExpression('declaration', 'noconflict'), $script_declarations['text/javascript'], ($removejQueryNoConflict == 1 ? false : true), $this->_verbose_array);

	                // remove potential jquery-noconflict.js (different combinations)

	                $number_removed = plgJQueryEasyHelper::search_and_delete('js', plgJQueryEasyHelper::getRegularExpression('js', 'noconflict'), $scripts, $this->_verbose_array);
	                if ($number_removed > 0) {
	                    plgJQueryEasyHelper::report($this->_verbose_array, 'deleted', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_REMOVEDNOCONFLICTSCRIPTS', $number_removed);
	                }
	            }

	            $replace_when_unique = $this->params->get('replacewhenunique' . $this->_suffix, 1);
	            $add_when_missing = $this->params->get('addwhenmissing' . $this->_suffix, 1);

	            // remove all references of the jQuery library except scripts to ignore

	            $ignoreScripts = trim((string)$this->params->get('ignorescripts' . $this->_suffix, ''));
	            if ($ignoreScripts) {
	                $ignoreScripts = array_map('trim', (array) explode("\n", $ignoreScripts));
	            }

	            $request_search_and_delete_results = ($add_when_missing && $replace_when_unique) ? false : true;

	            $removed_scripts = plgJQueryEasyHelper::search_and_delete('js', plgJQueryEasyHelper::getRegularExpression('js', 'jquery'), $scripts, $this->_verbose_array, $ignoreScripts, $request_search_and_delete_results);

	            $number_removed = $request_search_and_delete_results ? count($removed_scripts) : $removed_scripts;

	            if ($request_search_and_delete_results) {
	                if ($number_removed == 0 && !$add_when_missing) {
	                    $do_not_add_libraries = true;
	                    $do_not_add_stylesheets = true;
	                } else if ($number_removed == 1 && !$replace_when_unique) {
	                    $this->_jqpath = $removed_scripts[0];
	                    $move_unique_library = true;
	                    plgJQueryEasyHelper::report($this->_verbose_array, 'info', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_KEEPINGUNIQUELIBRARY', $this->_jqpath);
	                } else {
	                    if ($number_removed > 0) {
	                        plgJQueryEasyHelper::report($this->_verbose_array, 'deleted', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_REMOVEDJQUERY', $number_removed);
	                    }
	                }
	            } else {
	                if ($number_removed > 0) {
	                    plgJQueryEasyHelper::report($this->_verbose_array, 'deleted', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_REMOVEDJQUERY', $number_removed);
	                }
	            }

	            // remove all references of Migrate scripts

	            $number_removed = plgJQueryEasyHelper::search_and_delete('js', plgJQueryEasyHelper::getRegularExpression('js', 'migrate'), $scripts, $this->_verbose_array);
	            if ($number_removed > 0) {
	                plgJQueryEasyHelper::report($this->_verbose_array, 'deleted', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_REMOVEDMIGRATE', $number_removed);
	            }

	            // jQuery UI

	            if ($this->_usejQueryUI) {

	                // remove all references of the jQuery UI library

	                $request_search_and_delete_results = $replace_when_unique ? false : true;

	                $removed_scripts = plgJQueryEasyHelper::search_and_delete('js', plgJQueryEasyHelper::getRegularExpression('js', 'jqueryui'), $scripts, $this->_verbose_array, array(), $request_search_and_delete_results);

	                $number_removed = $request_search_and_delete_results ? count($removed_scripts) : $removed_scripts;

	                if ($request_search_and_delete_results) {
// 				        if ($number_removed == 0 && !$add_when_missing) {
//                             $do_not_add_libraries = true;
//                         }
	                    if ($number_removed == 1 && !$replace_when_unique) {
	                        $this->_jquipath = $removed_scripts[0];
	                        $move_unique_libraryui = true;
	                        plgJQueryEasyHelper::report($this->_verbose_array, 'info', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_KEEPINGUNIQUELIBRARYUI', $this->_jquipath);
	                    } else {
	                        if ($number_removed > 0) {
	                            plgJQueryEasyHelper::report($this->_verbose_array, 'deleted', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_REMOVEDJQUERYUI', $number_removed);
	                        }
	                    }
	                } else {
	                    if ($number_removed > 0) {
	                        plgJQueryEasyHelper::report($this->_verbose_array, 'deleted', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_REMOVEDJQUERYUI', $number_removed);
	                    }
	                }

	                // remove all references of the jQuery UI stylesheets

	                $removed_stylesheets = plgJQueryEasyHelper::search_and_delete('css', plgJQueryEasyHelper::getRegularExpression('css', 'jqueryui'), $styles, $this->_verbose_array, array(), $request_search_and_delete_results);

	                $number_removed = $request_search_and_delete_results ? count($removed_stylesheets) : $removed_stylesheets;

	                if ($request_search_and_delete_results) {
// 				    if ($number_removed == 0 && !$add_when_missing) {
// 				        $do_not_add_stylesheets = true;
// 				    }
	                    if ($number_removed == 1 && !$replace_when_unique) {
	                        $this->_jquicsspath = $removed_stylesheets[0];
	                        $move_unique_cssui = true;
	                        plgJQueryEasyHelper::report($this->_verbose_array, 'info', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_KEEPINGUNIQUECSSUI', $this->_jquicsspath);
	                    } else {
	                        if ($number_removed > 0) {
	                            plgJQueryEasyHelper::report($this->_verbose_array, 'deleted', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_REMOVEDJQUERYUICSS', $number_removed);
	                        }
	                    }
	                } else {
	                    if ($number_removed > 0) {
	                        plgJQueryEasyHelper::report($this->_verbose_array, 'deleted', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_REMOVEDJQUERYUICSS', $number_removed);
	                    }
	                }

	            } // END if usejQueryUI

	            // replace '$(document).ready(function()' or '$(document).ready(function($)' with 'jQuery(document).ready(function($)'

	            if ($this->params->get('replacedocumentready' . $this->_suffix, 1)) {
	                $script_declarations['text/javascript'] = preg_replace('#\$\(document\).ready\(function\([$]?\)#s', 'jQuery(document).ready(function($)', $script_declarations['text/javascript'], -1, $count);
	                if ($count > 0) {
	                    plgJQueryEasyHelper::report($this->_verbose_array, 'info', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_REPLACEDDOCUMENTREADY', $count);
	                }
	            }

	        } // END if usejQuery

	        if ($this->_showreport) { // report issues with Bootstrap

	        	$found_popper = false;
	        	$found_bundle = false;

	        	// Popper js path

	        	list($found_count, $found_matches) = plgJQueryEasyHelper::search_and_report('js', plgJQueryEasyHelper::getRegularExpression('js', 'popper'), $scripts, $this->_verbose_array);
        		if ($found_count > 0) {
        			plgJQueryEasyHelper::report($this->_verbose_array, 'info', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_MULTIPLEPOPPERJS', $found_count);
	        		foreach ($found_matches as $found_match) {
	        			plgJQueryEasyHelper::report($this->_verbose_array, 'info', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_FOUNDPOPPERJS', $found_match);
	        		}

	        		$found_popper = true;

	        		if ($found_count > 1) {
	        			plgJQueryEasyHelper::report($this->_verbose_array, 'error', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_FIXPOPPERISSUESGOPRO');
	        		}
	        	}

	        	// Bootstrap js path(s)

	        	list($found_count, $found_matches) = plgJQueryEasyHelper::search_and_report('js', plgJQueryEasyHelper::getRegularExpression('js', 'bootstrap'), $scripts, $this->_verbose_array);
	        	if ($found_count > 0) {
	        		plgJQueryEasyHelper::report($this->_verbose_array, 'info', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_MULTIPLEBOOTSTRAPJS', $found_count);
	        		foreach ($found_matches as $found_match) {
	        			plgJQueryEasyHelper::report($this->_verbose_array, 'info', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_FOUNDBOOTSTRAPJS', $found_match);

	        			if (strpos($found_match, 'bundle') !== false) {
	        				$found_bundle = true;
	        			}
	        		}

	        		if ($found_count > 1) {
	        			plgJQueryEasyHelper::report($this->_verbose_array, 'error', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_FIXBOOTSTRAPISSUESGOPRO');
	        		}
	        	}

	        	if ($found_popper && $found_bundle) {
	        		plgJQueryEasyHelper::report($this->_verbose_array, 'error', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_FIXBUNDLEISSUESGOPRO');
	        	}

	        	// Bootstrap css path(s)

	        	list($found_count, $found_matches) = plgJQueryEasyHelper::search_and_report('css', plgJQueryEasyHelper::getRegularExpression('css', 'bootstrap'), $styles, $this->_verbose_array);
	        	if ($found_count > 0) {
	        		$safe_urls = array('media/jui/css/bootstrap-responsive', 'media/jui/css/bootstrap-extended', 'media/jui/css/bootstrap-rtl');

	        		plgJQueryEasyHelper::report($this->_verbose_array, 'info', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_MULTIPLEBOOTSTRAPCSS', $found_count);
	        		foreach ($found_matches as $found_match) {
	        			plgJQueryEasyHelper::report($this->_verbose_array, 'info', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_FOUNDBOOTSTRAPCSS', $found_match);

	        			foreach ($safe_urls as $safe_url) {
	        				if (strpos($found_match, $safe_url) !== false) {
	        					$found_count--;
	        				}
	        			}
	        		}

	        		if ($found_count > 1) {
	        			plgJQueryEasyHelper::report($this->_verbose_array, 'error', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_FIXBOOTSTRAPISSUESGOPRO');
	        		}
	        	}
	        } // END report issues for Bootstrap

	        $remainingScripts = array();

	        // remaining scripts from the plugin

	        $remainingScriptsParam = trim( (string) $this->params->get('stripremainingscripts' . $this->_suffix, ''));
	        if ($remainingScriptsParam) {
	            $remainingScripts = array_map('trim', (array) explode("\n", $remainingScriptsParam));
	        }

	        // remove remaining scripts

	        if (!empty($remainingScripts)) {
	            foreach ($remainingScripts as $remainingScript) {

	                $number_removed = plgJQueryEasyHelper::search_and_delete('js', preg_quote($remainingScript, '/'), $scripts, $this->_verbose_array);
	                
	                if ($number_removed > 0) {
	                    plgJQueryEasyHelper::report($this->_verbose_array, 'deleted', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_STRIPPEDREMAININGSCRIPT', $remainingScript, $number_removed);
	                }
	            }
	        }

	        $remainingStylesheets = array();

	        // remaining styles from the plugin

	        $remainingStylesheetsParam = trim( (string) $this->params->get('stripremainingcss' . $this->_suffix, ''));
	        if ($remainingStylesheetsParam) {
	            $remainingStylesheets = array_map('trim', (array) explode("\n", $remainingStylesheetsParam));
	        }

	        // remove remaining stylesheets

	        if (!empty($remainingStylesheets)) {
	            foreach ($remainingStylesheets as $remainingStylesheet) {

	                $number_removed = plgJQueryEasyHelper::search_and_delete('css', preg_quote($remainingStylesheet, '/'), $styles, $this->_verbose_array);
	                
	                if ($number_removed > 0) {
	                    plgJQueryEasyHelper::report($this->_verbose_array, 'deleted', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_STRIPPEDREMAININGCSS', $remainingStylesheet, $number_removed);
	                }
	            }
	        }

	        // additions after cleanup

	        $options = array();
	        if ($this->_versioning) {
	            $options['version'] = 'auto';
	        }

	        // add all scripts

	        if ($this->_usejQuery) {
	            if ($this->_jqpath) {
	                if ($do_not_add_libraries) {
	                    plgJQueryEasyHelper::report($this->_verbose_array, 'info', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_NOJQUERYLIBRARIESADDED');
	                } else {
	                    if (version_compare(JVERSION, '3.7.0', 'ge')) {
	                        $new_scripts[$this->_jqpath] = array('type' => 'text/javascript', 'defer' => false, 'async' => false, 'options' => (plgJQueryEasyHelper::isInternal($this->_jqpath) ? $options : array()));
	                    } else {
	                    	$path_version = $this->_jqpath;
	                    	if ($this->_versioning && !empty($this->_version) && strpos($this->_jqpath, '?') === false && plgJQueryEasyHelper::isInternal($this->_jqpath)) {
	                    		$path_version .= '?' . $this->_version;
	                    	}
	                    	$new_scripts[$path_version] = array('mime' => 'text/javascript', 'defer' => false, 'async' => false);
	                    }
	                    if ($move_unique_library) {
	                        plgJQueryEasyHelper::report($this->_verbose_array, 'info', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_MOVEDJQUERY', $this->_jqpath);
	                    } else {
	                        plgJQueryEasyHelper::report($this->_verbose_array, 'added', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_ADDEDJQUERY', '<a href="'.$this->_jqpath.'" target="_blank">'.$this->_jqpath.'</a>');
	                    }
	                }
	            } else {
	                plgJQueryEasyHelper::report($this->_verbose_array, 'error', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_ERRORADDINGJQUERY');
	            }

	            if ($this->_jqmigratepath) {
	                if ($do_not_add_libraries) { // no need to add Migrate if jQuery is not even loaded
	                    plgJQueryEasyHelper::report($this->_verbose_array, 'info', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_NOMIGRATEADDED');
	                } else {
	                    if (version_compare(JVERSION, '3.7.0', 'ge')) {
	                        $new_scripts[$this->_jqmigratepath] = array('type' => 'text/javascript', 'defer' => false, 'async' => false, 'options' => (plgJQueryEasyHelper::isInternal($this->_jqmigratepath) ? $options : array()));
	                    } else {
	                    	$path_version = $this->_jqmigratepath;
	                    	if ($this->_versioning && !empty($this->_version) && strpos($this->_jqmigratepath, '?') === false && plgJQueryEasyHelper::isInternal($this->_jqmigratepath)) {
	                    		$path_version .= '?' . $this->_version;
	                    	}
	                    	$new_scripts[$path_version] = array('mime' => 'text/javascript', 'defer' => false, 'async' => false);
	                    }
	                    plgJQueryEasyHelper::report($this->_verbose_array, 'added', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_ADDEDJQUERYMIGRATE', '<a href="'.$this->_jqmigratepath.'" target="_blank">'.$this->_jqmigratepath.'</a>');
	                }
	            }

	            if ($this->params->get('addnoconflict' . $this->_suffix, 2) == 1) {
	                if ($do_not_add_libraries) {
	                    plgJQueryEasyHelper::report($this->_verbose_array, 'info', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_NONOCONFLICTDECLARATIONADDED');
	                } else {
	                    $script_declarations['text/javascript'] = 'jQuery.noConflict(); ' . $script_declarations['text/javascript'];
	                    plgJQueryEasyHelper::report($this->_verbose_array, 'added', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_ADDEDNOCONFLICTDECLARATION');
	                }
	            } else if ($this->params->get('addnoconflict' . $this->_suffix, 2) == 2 && $this->_jqnoconflictpath) {
	                if ($do_not_add_libraries) {
	                    plgJQueryEasyHelper::report($this->_verbose_array, 'info', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_NONOCONFLICTSCRIPTADDED');
	                } else {
	                	if (version_compare(JVERSION, '3.7.0', 'ge')) {
	                		$new_scripts[$this->_jqnoconflictpath] = array('type' => 'text/javascript', 'defer' => false, 'async' => false, 'options' => $options);
	                	} else {
	                		$path_version = $this->_jqnoconflictpath;
	                		if ($this->_versioning && !empty($this->_version) && strpos($this->_jqnoconflictpath, '?') === false) {
	                			$path_version .= '?' . $this->_version;
	                		}
	                		$new_scripts[$path_version] = array('mime' => 'text/javascript', 'defer' => false, 'async' => false);
	                	}
	                    plgJQueryEasyHelper::report($this->_verbose_array, 'added', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_ADDEDNOCONFLICTSCRIPT', $this->_jqnoconflictpath);
	                }
	            }

	            if ($this->_usejQueryUI) {
	                if ($this->_jquipath) {
	                    if ($do_not_add_libraries) {
	                        plgJQueryEasyHelper::report($this->_verbose_array, 'info', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_NOJQUERYUILIBRARYADDED');
	                    } else {
	                        if (version_compare(JVERSION, '3.7.0', 'ge')) {
	                            $new_scripts[$this->_jquipath] = array('type' => 'text/javascript', 'defer' => false, 'async' => false, 'options' => (plgJQueryEasyHelper::isInternal($this->_jquipath) ? $options : array()));
	                        } else {
	                        	$path_version = $this->_jquipath;
	                        	if ($this->_versioning && !empty($this->_version) && strpos($this->_jquipath, '?') === false && plgJQueryEasyHelper::isInternal($this->_jquipath)) {
	                        		$path_version .= '?' . $this->_version;
	                        	}
	                        	$new_scripts[$path_version] = array('mime' => 'text/javascript', 'defer' => false, 'async' => false);
	                        }
	                        if ($move_unique_libraryui) {
	                            plgJQueryEasyHelper::report($this->_verbose_array, 'info', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_MOVEDJQUERYUI', $this->_jquipath);
	                        } else {
	                            plgJQueryEasyHelper::report($this->_verbose_array, 'added', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_ADDEDJQUERYUI', '<a href="'.$this->_jquipath.'" target="_blank">'.$this->_jquipath.'</a>');
	                        }
	                    }
	                } else {
	                    plgJQueryEasyHelper::report($this->_verbose_array, 'error', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_ERRORADDINGJQUERYUI');
	                }
	            }
	        }

	        if (!empty($new_scripts)) {
	            plgJQueryEasyHelper::report($this->_verbose_array, 'message', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_REORDEREDLIBRARIES');
	        }

	        // add all styles

	        if ($this->_usejQuery) {

	            if ($this->_usejQueryUI) {
	                if ($this->_jquicsspath) {
	                    if ($do_not_add_stylesheets) {
	                        plgJQueryEasyHelper::report($this->_verbose_array, 'info', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_NOJQUERYUISTYLESHEETADDED');
	                    } else {
	                        if (version_compare(JVERSION, '3.7.0', 'ge')) {
	                            $new_styles[$this->_jquicsspath] = array('type' => 'text/css', 'options' => (plgJQueryEasyHelper::isInternal($this->_jquicsspath) ? $options : array()));
	                        } else {
	                        	$path_version = $this->_jquicsspath;
	                        	if ($this->_versioning && !empty($this->_version) && strpos($this->_jquicsspath, '?') === false && plgJQueryEasyHelper::isInternal($this->_jquicsspath)) {
	                        		$path_version .= '?' . $this->_version;
	                        	}
	                        	$new_styles[$path_version] = array('mime' => 'text/css', 'media' => null, 'attribs' => array());
	                        }
	                        if ($move_unique_cssui) {
	                            plgJQueryEasyHelper::report($this->_verbose_array, 'info', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_MOVEDJQUERYUICSS', $this->_jquicsspath);
	                        } else {
	                            plgJQueryEasyHelper::report($this->_verbose_array, 'added', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_ADDEDJQUERYUICSS', '<a href="'.$this->_jquicsspath.'" target="_blank">'.$this->_jquicsspath.'</a>');
	                        }
	                    }
	                } else {
	                    if ($this->params->get('jqueryuitheme' . $this->_suffix, 'none') != 'none') {
	                        plgJQueryEasyHelper::report($this->_verbose_array, 'error', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_ERRORADDINGJQUERYUICSS');
	                    }
	                }
	            }
	        }

	        // add all scripts

	        foreach($this->_supplement_scripts as $path) {
	            if (version_compare(JVERSION, '3.7.0', 'ge')) {
	                $new_scripts[$path] = array('type' => 'text/javascript', 'options' => (plgJQueryEasyHelper::isInternal($path) ? $options : array()));
	            } else {
	            	$path_version = $path;
	            	if ($this->_versioning && !empty($this->_version) && strpos($path, '?') === false && plgJQueryEasyHelper::isInternal($path)) {
	            		$path_version .= '?' . $this->_version;
	            	}
	            	$new_scripts[$path_version] = array('mime' => 'text/javascript', 'defer' => false, 'async' => false);
	            }
	            plgJQueryEasyHelper::report($this->_verbose_array, 'added', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_ADDEDSCRIPT', $path);
	        }

	        // add all styles

	        foreach($this->_supplement_stylesheets as $path) {
	            if (version_compare(JVERSION, '3.7.0', 'ge')) {
	                $new_styles[$path] = array('type' => 'text/css', 'options' => (plgJQueryEasyHelper::isInternal($path) ? $options : array()));
	            } else {
	            	$path_version = $path;
	            	if ($this->_versioning && !empty($this->_version) && strpos($path, '?') === false && plgJQueryEasyHelper::isInternal($path)) {
	            		$path_version .= '?' . $this->_version;
	            	}
	            	$new_styles[$path_version] = array('mime' => 'text/css', 'media' => null, 'attribs' => array());
	            }
	            plgJQueryEasyHelper::report($this->_verbose_array, 'added', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_ADDEDSTYLESHEET', $path);
	        }

	        // add all script declarations

	        // script declaration from the plugin
	        $javascript_declaration = trim( (string) $this->params->get('addjavascriptdeclaration' . $this->_suffix, ''));
	        if (!empty($javascript_declaration)) {
	            $script_declarations['text/javascript'] .= $javascript_declaration;
	            if ($this->_showreport) {
	                $lines = array_map('trim', (array) explode("\n", $javascript_declaration));
	                plgJQueryEasyHelper::report($this->_verbose_array, 'added', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_ADDEDSCRIPTDECLARATION', $lines[0]);
	            }
	        }

	        // add all style declarations

	        $css_declaration = trim( (string) $this->params->get('addcssdeclaration' . $this->_suffix, ''));
	        if (!empty($css_declaration)) {
	            $style_declarations['text/css'] .= $css_declaration;
	            if ($this->_showreport) {
	                $lines = array_map('trim', (array) explode("\n", $css_declaration));
	                plgJQueryEasyHelper::report($this->_verbose_array, 'added', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_ADDEDSTYLESHEETDECLARATION', $lines[0]);
	            }
	        }

	    } else { // the whole document is scanned

	        // at this point, jQuery and MooTools libraries are loaded in the wrong order, if jQuery is enabled
	        // we have jQuery, MooTools and other libraries loaded in that order
	        // take all 'media/system/js' libraries and put them in front of all others

	        // make sure we start with all jQuery Easy scripts

	        $scripts_jqeasy = array();

	        foreach ($scripts as $url => $type) {
	            if (preg_match('#JQEASY_#s', $url)) {
	                $scripts_jqeasy[$url] = $type;
	            }
	        }

	        if (!empty($scripts_jqeasy)) {

	            foreach ($scripts_jqeasy as $url_jqeasy => $type_jqeasy) {
	                $new_scripts[$url_jqeasy] = $type_jqeasy;
	                unset($scripts[$url_jqeasy]);
	            }

	            // then with MooTools and all system scripts

	            $quoted_path = preg_quote('media/system/js/', '/');
	            foreach ($scripts as $url => $type) {
	                if (preg_match('#'.$quoted_path.'#s', $url)) {
	                    $new_scripts[$url] = $type;
	                    unset($scripts[$url]);
	                }
	            }

	            // make sure we follow with all media/jui/js scripts

	            $quoted_path = preg_quote('media/jui/js/', '/');
	            foreach ($scripts as $url => $type) {
	                if (preg_match('#'.$quoted_path.'#s', $url)) {
	                    $new_scripts[$url] = $type;
	                    unset($scripts[$url]);
	                }
	            }

	            plgJQueryEasyHelper::report($this->_verbose_array, 'message', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_REORDEREDLIBRARIES');
	        }
	    }

	    JFactory::getDocument()->_scripts = array_merge($new_scripts, $scripts);
	    
// 	    var_dump(JFactory::getDocument()->_scripts);

	    if (trim($script_declarations['text/javascript']) != '') {
	    	JFactory::getDocument()->_script['text/javascript'] = $script_declarations['text/javascript'];
	    } else {
	    	// after removal of scripts, we may end up with nothing
	    	if (isset(JFactory::getDocument()->_script['text/javascript'])) {
	    		unset(JFactory::getDocument()->_script['text/javascript']);
	    	}
	    }
	    
// 	    var_dump(preg_replace('!\s+!', ' ', JFactory::getDocument()->_script['text/javascript']));

	    JFactory::getDocument()->_styleSheets = array_merge($new_styles, $styles);
	    
//  	var_dump(JFactory::getDocument()->_styleSheets);

	    if (trim($style_declarations['text/css']) != '') {
	    	JFactory::getDocument()->_style['text/css'] = $style_declarations['text/css'];
	    }

//  	var_dump(JFactory::getDocument()->_style['text/css']);

	    $time_end = microtime(true);
	    $this->_timebeforecompilehead = $time_end - $time_start;
	}

	function onAfterRender()
	{
		if (!$this->_enabled) {
			return;
		}

		// timing starts
		
		$time_start = microtime(true);

		//$body = JResponse::getBody();

		if (!$this->_apionly) {
		    
		    switch ((int)$this->params->get('pagescan', 0)) 
		    {
		        case 1: // head only
		            
		            preg_match('/<head>([\s\S]*)<\/head>/s', JResponse::getBody(), $match);
		            $body = $match[0]; // keep the tags
		            
		            break;
		            
		        default: // whole page scan
		            
		            $body = JResponse::getBody();
		    }

    		$remove_empty_scripts = false;
    		$remove_empty_links = false;

    		$remainingScriptsParam = trim( (string) $this->params->get('stripremainingscripts' . $this->_suffix, ''));
    		if ($remainingScriptsParam) {
    		    $remainingScripts = array_map('trim', (array) explode("\n", $remainingScriptsParam));
    		    if (!empty($remainingScripts)) {
    		        foreach ($remainingScripts as $remainingScript) {
    					
    		            $number_removed = plgJQueryEasyHelper::search_and_delete('js', preg_quote($remainingScript, '/'), $body, $this->_verbose_array);
    					
    					if ($number_removed > 0) {
    					    $remove_empty_scripts = true;
    					    plgJQueryEasyHelper::report($this->_verbose_array, 'deleted', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_STRIPPEDREMAININGSCRIPT', $remainingScript, $number_removed);
    					}
    				}
    			}
    		}

    		$remainingStylesheetsParam = trim( (string) $this->params->get('stripremainingcss' . $this->_suffix, ''));
    		if ($remainingStylesheetsParam) {
    		    $remainingStylesheets = array_map('trim', (array) explode("\n", $remainingStylesheetsParam));
    		    if (!empty($remainingStylesheets)) {
    		        foreach ($remainingStylesheets as $remainingStylesheet) {

    					$number_removed = plgJQueryEasyHelper::search_and_delete('css', preg_quote($remainingStylesheet, '/'), $body, $this->_verbose_array);
    					
    					if ($number_removed > 0) {
    					    $remove_empty_links = true;
    					    plgJQueryEasyHelper::report($this->_verbose_array, 'deleted', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_STRIPPEDREMAININGCSS', $remainingStylesheet, $number_removed);
    					}
    				}
    			}
    		}
    		
    		// jQuery

    		if ($this->_usejQuery) {

    			$removejQueryNoConflict = $this->params->get('removenoconflict' . $this->_suffix, 1);
    			if ($removejQueryNoConflict == 1 || $removejQueryNoConflict == 2) {

    				// remove all '...jQuery.noConflict(...);' or '... $.noConflict(...);'

    			    plgJQueryEasyHelper::search_and_replace_noconflict(plgJQueryEasyHelper::getRegularExpression('declaration', 'noconflict'), $body, ($removejQueryNoConflict == 1 ? false : true), $this->_verbose_array, true);

    				// remove potential jquery-noconflict.js (different combinations)

    				$number_removed = plgJQueryEasyHelper::search_and_delete('js', plgJQueryEasyHelper::getRegularExpression('js', 'noconflict'), $body, $this->_verbose_array);

    				if ($number_removed > 0) {
    				    $remove_empty_scripts = true;
    				    plgJQueryEasyHelper::report($this->_verbose_array, 'deleted', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_REMOVEDNOCONFLICTSCRIPTS', $number_removed);
    				}
    			}

    			$do_not_add_libraries = false;
    			$do_not_add_stylesheets = false;
    			$move_unique_library = false;

    			$replace_when_unique = $this->params->get('replacewhenunique' . $this->_suffix, 1);
    			$add_when_missing = $this->params->get('addwhenmissing' . $this->_suffix, 1);

    			// remove all other references to jQuery library except some

    			$ignoreScripts = trim( (string) $this->params->get('ignorescripts' . $this->_suffix, ''));
    			if ($ignoreScripts) {
    				$ignoreScripts = array_map('trim', (array) explode("\n", $ignoreScripts));
    			}

    			$request_search_and_delete_results = ($add_when_missing && $replace_when_unique) ? false : true;

    			$removed_scripts = plgJQueryEasyHelper::search_and_delete('js', plgJQueryEasyHelper::getRegularExpression('js', 'jquery'), $body, $this->_verbose_array, $ignoreScripts, $request_search_and_delete_results);

    			$number_removed = $request_search_and_delete_results ? count($removed_scripts) : $removed_scripts;

    			if ($request_search_and_delete_results) {
    			    if ($number_removed == 0 && !$add_when_missing) {
    			        $do_not_add_libraries = true;
    			        $do_not_add_stylesheets = true;
    			        //plgJQueryEasyHelper::report($this->_verbose_array, 'info', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_NOJQUERYLIBRARIESADDED');
    			    } else if ($number_removed == 1 && !$replace_when_unique) {
    			        $this->_jqpath = $removed_scripts[0];
    			        $move_unique_library = true;
    			        $remove_empty_scripts = true;
    			        plgJQueryEasyHelper::report($this->_verbose_array, 'info', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_KEEPINGUNIQUELIBRARY', $this->_jqpath);
    			    } else {
    			        if ($number_removed > 0) {
    			            $remove_empty_scripts = true;
    			            foreach ($removed_scripts as $removed_script) {
    			                plgJQueryEasyHelper::report($this->_verbose_array, 'deleted', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_REMOVEDJQUERYLIBRARY', $removed_script);
    			            }
    			        }
    			    }
    			} else {
    			    if ($number_removed > 0) {
    			        $remove_empty_scripts = true;
    			        plgJQueryEasyHelper::report($this->_verbose_array, 'deleted', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_REMOVEDJQUERY', $number_removed);
    			    }
    			}

    			// use jQuery version set in the plugin
    			if ($this->_jqpath) {
    				if ($do_not_add_libraries) {
    					$body = preg_replace('#([\\/a-zA-Z0-9_:\.~-]*)JQEASY_JQLIB#', 'GARBAGE', $body, 1);
    					plgJQueryEasyHelper::report($this->_verbose_array, 'info', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_NOJQUERYLIBRARIESADDED');
    					$remove_empty_scripts = true;
    				} else {
    					$body = preg_replace('#([\\/a-zA-Z0-9_:\.~-]*)JQEASY_JQLIB#', $this->_jqpath, $body, 1);
    					if ($move_unique_library) {
    					    plgJQueryEasyHelper::report($this->_verbose_array, 'info', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_MOVEDJQUERY', $this->_jqpath);
    					} else {
    					    plgJQueryEasyHelper::report($this->_verbose_array, 'added', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_ADDEDJQUERY', '<a href="'.$this->_jqpath.'" target="_blank">'.$this->_jqpath.'</a>');
    					}
    				}
    			} else {
    			    plgJQueryEasyHelper::report($this->_verbose_array, 'error', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_ERRORADDINGJQUERY');
    			}

                // remove all references of the Migrate scripts

    			$number_removed = plgJQueryEasyHelper::search_and_delete('js', plgJQueryEasyHelper::getRegularExpression('js', 'migrate'), $body, $this->_verbose_array);

    			// TODO? replace when unique

    			if ($number_removed > 0) {
    			    $remove_empty_scripts = true;
    			    plgJQueryEasyHelper::report($this->_verbose_array, 'deleted', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_REMOVEDMIGRATE', $number_removed);
    			}

    			// use jQuery Migrate
    			if ($this->_jqmigratepath) {
    			    if ($do_not_add_libraries) { // no need to add Migrate if jQuery is not even loaded
    			        $body = preg_replace('#([\\/a-zA-Z0-9_:\.~-]*)JQEASY_JQMIGRATELIB#', 'GARBAGE', $body, 1);
    			        plgJQueryEasyHelper::report($this->_verbose_array, 'info', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_NOMIGRATEADDED');
    			        $remove_empty_scripts = true;
    			    } else {
    					$body = preg_replace('#([\\/a-zA-Z0-9_:\.~-]*)JQEASY_JQMIGRATELIB#', $this->_jqmigratepath, $body, 1);
    					plgJQueryEasyHelper::report($this->_verbose_array, 'added', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_ADDEDJQUERYMIGRATE', '<a href="'.$this->_jqmigratepath.'" target="_blank">'.$this->_jqmigratepath.'</a>');
    			    }
    			}

    			// replace deleted occurences
    			$addjQueryNoConflict = $this->params->get('addnoconflict' . $this->_suffix, 2);
    			if ($addjQueryNoConflict == 1) {
    				if ($do_not_add_libraries) {
    				    $body = preg_replace('#JQEASY_JQNOCONFLICT#', '', $body, 1);
    				    plgJQueryEasyHelper::report($this->_verbose_array, 'info', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_NONOCONFLICTDECLARATIONADDED');
    				} else {
    					$body = preg_replace('#JQEASY_JQNOCONFLICT#', 'jQuery.noConflict();', $body, 1); // add unique jQuery.noConflict();
    					plgJQueryEasyHelper::report($this->_verbose_array, 'added', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_ADDEDNOCONFLICTDECLARATION');
    				}
    			} elseif ($addjQueryNoConflict == 2) {
    				if ($do_not_add_libraries) {
    				    $body = preg_replace('#([\\/a-zA-Z0-9_:\.~-]*)JQEASY_JQNOCONFLICT#', 'GARBAGE', $body, 1);
    				    plgJQueryEasyHelper::report($this->_verbose_array, 'info', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_NONOCONFLICTSCRIPTADDED');
    					$remove_empty_scripts = true;
    				} else {
    					$body = preg_replace('#([\\/a-zA-Z0-9_:\.~-]*)JQEASY_JQNOCONFLICT#', $this->_jqnoconflictpath, $body, 1); // add jquerynoconflict.js
    					plgJQueryEasyHelper::report($this->_verbose_array, 'added', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_ADDEDNOCONFLICTSCRIPT', $this->_jqnoconflictpath);
    				}
    			}

    			// replace '$(document).ready(function()' or '$(document).ready(function($)' with 'jQuery(document).ready(function($)'
    			if ($this->params->get('replacedocumentready' . $this->_suffix, 1)) {

    			    $number_replaced = plgJQueryEasyHelper::search_and_replace('\$\(document\).ready\(function\([$]?\)', $body, 'jQuery(document).ready(function($)');
    				
    				if ($number_replaced > 0) {
    				    plgJQueryEasyHelper::report($this->_verbose_array, 'info', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_REPLACEDDOCUMENTREADY', $number_replaced);
    				}
    			}

    			if ($this->_usejQueryUI) {

        			//$do_not_add_libraries = false;
    				$move_unique_libraryui = false;

    				// remove all other references to jQuery UI library

    				$request_search_and_delete_results = $replace_when_unique ? false : true;

    				$removed_scripts = plgJQueryEasyHelper::search_and_delete('js', plgJQueryEasyHelper::getRegularExpression('js', 'jqueryui'), $body, $this->_verbose_array, array(), $request_search_and_delete_results);

    				$number_removed = $request_search_and_delete_results ? count($removed_scripts) : $removed_scripts;

    				if ($request_search_and_delete_results) {
// 				    if ($number_removed == 0 && !$add_when_missing) {
// 				        $do_not_add_libraries = true;
// 				    }
    				    if ($number_removed == 1 && !$replace_when_unique) {
    				        $this->_jquipath = $removed_scripts[0];
    				        $move_unique_libraryui = true;
    				        $remove_empty_scripts = true;
    				        plgJQueryEasyHelper::report($this->_verbose_array, 'info', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_KEEPINGUNIQUELIBRARYUI', $this->_jquipath);
    				    } else {
    				        if ($number_removed > 0) {
    				            $remove_empty_scripts = true;
    				            foreach ($removed_scripts as $removed_script) {
    				                plgJQueryEasyHelper::report($this->_verbose_array, 'deleted', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_REMOVEDJQUERYUILIBRARY', $removed_script);
    				            }
    				        }
    				    }
    				} else {
    				    if ($number_removed > 0) {
    				        $remove_empty_scripts = true;
    				        plgJQueryEasyHelper::report($this->_verbose_array, 'deleted', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_REMOVEDJQUERYUI', $number_removed);
    				    }
    				}

    				// use jQuery UI version set in the plugin
    				if ($this->_jquipath) {
    					if ($do_not_add_libraries) {
    					    $body = preg_replace('#([\\/a-zA-Z0-9_:\.~-]*)JQEASY_JQUILIB#', 'GARBAGE', $body, 1);
    					    plgJQueryEasyHelper::report($this->_verbose_array, 'info', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_NOJQUERYUILIBRARYADDED');
    						$remove_empty_scripts = true;
    					} else {
    						$body = preg_replace('#([\\/a-zA-Z0-9_:\.~-]*)JQEASY_JQUILIB#', $this->_jquipath, $body, 1);
    						if ($move_unique_libraryui) {
    						    plgJQueryEasyHelper::report($this->_verbose_array, 'info', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_MOVEDJQUERYUI', $this->_jquipath);
    						} else {
    						    plgJQueryEasyHelper::report($this->_verbose_array, 'added', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_ADDEDJQUERYUI', '<a href="'.$this->_jquipath.'" target="_blank">'.$this->_jquipath.'</a>');
    						}
    					}
    				} else {
    				    plgJQueryEasyHelper::report($this->_verbose_array, 'error', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_ERRORADDINGJQUERYUI');
    				}

    				// remove all other references to jQuery UI stylesheets

        			//$do_not_add_stylesheets = $do_not_add_libraries;
    				$move_unique_cssui = false;

    				$removed_stylesheets = plgJQueryEasyHelper::search_and_delete('css', plgJQueryEasyHelper::getRegularExpression('css', 'jqueryui'), $body, $this->_verbose_array, array(), $request_search_and_delete_results);

    				$number_removed = $request_search_and_delete_results ? count($removed_stylesheets) : $removed_stylesheets;

    				if ($request_search_and_delete_results) {
// 					    if ($number_removed == 0 && !$add_when_missing) {
// 					        $do_not_add_stylesheets = true;
// 					    }
    				    if ($number_removed == 1 && !$replace_when_unique) {
    				        $this->_jquicsspath = $removed_stylesheets[0];
    				        $move_unique_cssui = true;
    				        $remove_empty_links = true;
    				        plgJQueryEasyHelper::report($this->_verbose_array, 'info', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_KEEPINGUNIQUECSSUI', $this->_jquicsspath);
    				    } else {
    				        if ($number_removed > 0) {
    				            $remove_empty_links = true;
    				            foreach ($removed_stylesheets as $removed_stylesheet) {
    				                plgJQueryEasyHelper::report($this->_verbose_array, 'deleted', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_REMOVEDJQUERYUICSSLINK', $removed_stylesheet);
    				            }
    				        }
    				    }
    				} else {
    				    if ($number_removed > 0) {
    				        $remove_empty_links = true;
    				        plgJQueryEasyHelper::report($this->_verbose_array, 'deleted', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_REMOVEDJQUERYUICSS', $number_removed);
    				    }
    				}

    				// use jQuery UI CSS set in the plugin
    				if ($this->_jquicsspath) {
    				    if ($do_not_add_stylesheets) {
    					    $body = preg_replace('#([\\/a-zA-Z0-9_:\.~-]*)JQEASY_JQUICSS#', 'GARBAGE', $body, 1);
    					    plgJQueryEasyHelper::report($this->_verbose_array, 'info', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_NOJQUERYUISTYLESHEETADDED');
    						$remove_empty_links = true;
    					} else {
    						$body = preg_replace('#([\\/a-zA-Z0-9_:\.~-]*)JQEASY_JQUICSS#', $this->_jquicsspath, $body, 1);
   							if ($move_unique_cssui) {
   							    plgJQueryEasyHelper::report($this->_verbose_array, 'info', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_MOVEDJQUERYUICSS', $this->_jquicsspath);
   							} else {
  							    plgJQueryEasyHelper::report($this->_verbose_array, 'added', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_ADDEDJQUERYUICSS', '<a href="'.$this->_jquicsspath.'" target="_blank">'.$this->_jquicsspath.'</a>');
   							}
    					}
    				} else {
    					if ($this->params->get('jqueryuitheme' . $this->_suffix, 'none') != 'none') {
    						plgJQueryEasyHelper::report($this->_verbose_array, 'error', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_ERRORADDINGJQUERYUICSS');
    					}
    				}
    			}
    		} // END if $this->jQuery
    		
    		// Bootstrap

    		if ($this->_showreport) { // report issues with Bootstrap

    			$found_popper = false;
    			$found_bundle = false;

    			// Popper js path

    			list($found_count, $found_matches) = plgJQueryEasyHelper::search_and_report('js', plgJQueryEasyHelper::getRegularExpression('js', 'popper'), $body, $this->_verbose_array);
    			if ($found_count > 0) {
    				plgJQueryEasyHelper::report($this->_verbose_array, 'info', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_MULTIPLEPOPPERJS', $found_count);
    				foreach ($found_matches as $found_match) {
    					plgJQueryEasyHelper::report($this->_verbose_array, 'info', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_FOUNDPOPPERJS', $found_match);
    				}

    				$found_popper = true;

    				if ($found_count > 1) {
    					plgJQueryEasyHelper::report($this->_verbose_array, 'error', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_FIXPOPPERISSUESGOPRO');
    				}
    			}

    			// Bootstrap js path(s)

    			list($found_count, $found_matches) = plgJQueryEasyHelper::search_and_report('js', plgJQueryEasyHelper::getRegularExpression('js', 'bootstrap'), $body, $this->_verbose_array);
    			if ($found_count > 0) {
    				plgJQueryEasyHelper::report($this->_verbose_array, 'info', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_MULTIPLEBOOTSTRAPJS', $found_count);
    				foreach ($found_matches as $found_match) {
    					plgJQueryEasyHelper::report($this->_verbose_array, 'info', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_FOUNDBOOTSTRAPJS', $found_match);

    					if (strpos($found_match, 'bundle') !== false) {
    						$found_bundle = true;
    					}
    				}

    				if ($found_count > 1) {
    					plgJQueryEasyHelper::report($this->_verbose_array, 'error', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_FIXBOOTSTRAPISSUESGOPRO');
    				}
    			}

    			if ($found_popper && $found_bundle) {
    				plgJQueryEasyHelper::report($this->_verbose_array, 'error', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_FIXBUNDLEISSUESGOPRO');
    			}

    			// Bootstrap css path(s)

    			list($found_count, $found_matches) = plgJQueryEasyHelper::search_and_report('css', plgJQueryEasyHelper::getRegularExpression('css', 'bootstrap'), $body, $this->_verbose_array);
    			if ($found_count > 0) {
    				$safe_urls = array('media/jui/css/bootstrap-responsive', 'media/jui/css/bootstrap-extended', 'media/jui/css/bootstrap-rtl');

    				plgJQueryEasyHelper::report($this->_verbose_array, 'info', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_MULTIPLEBOOTSTRAPCSS', $found_count);
    				foreach ($found_matches as $found_match) {
    					plgJQueryEasyHelper::report($this->_verbose_array, 'info', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_FOUNDBOOTSTRAPCSS', $found_match);

    					foreach ($safe_urls as $safe_url) {
    						if (strpos($found_match, $safe_url) !== false) {
    							$found_count--;
    						}
    					}
    				}

    				if ($found_count > 1) {
    					plgJQueryEasyHelper::report($this->_verbose_array, 'error', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_FIXBOOTSTRAPISSUESGOPRO');
    				}
    			}
    		} // END report issues with Bootstrap

    		// remove all obsolete script tags
    		if ($remove_empty_scripts) {

    			$number_removed = plgJQueryEasyHelper::search_and_replace('<script[^>]*GARBAGE[^>]*></script>', $body, '');
    			
    			if ($number_removed > 0) {
    			    plgJQueryEasyHelper::report($this->_verbose_array, 'deleted', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_REMOVEDEMPTYSCRIPTTAGS', $number_removed);
    			}
    		}

    		// remove all obsolete link tags
    		if ($remove_empty_links) {

    		    $number_removed = plgJQueryEasyHelper::search_and_replace('<link[^>]*GARBAGE[^>]*/>', $body, '');
    		    
    		    if ($number_removed > 0) {
    			    plgJQueryEasyHelper::report($this->_verbose_array, 'deleted', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_REMOVEDEMPTYLINKTAGS', $count);
    			}
    		}

    		// all scripts and stylesheets are added here instead of earlier so they don't get checked by the plugin

    		if (!empty($this->_supplement_scripts)) {
    			foreach($this->_supplement_scripts as $path) {
    				$body = preg_replace('#([\\/a-zA-Z0-9_:\.~-]*)ADD_SCRIPT_HERE#', $path, $body, 1);
    				plgJQueryEasyHelper::report($this->_verbose_array, 'added', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_ADDEDSCRIPT', $path);
    			}
    		}

    		$javascript_declaration = trim( (string) $this->params->get('addjavascriptdeclaration' . $this->_suffix, ''));
    		if (!empty($javascript_declaration)) {
    			$body = preg_replace('#ADD_SCRIPT_DECLARATION_HERE#', $javascript_declaration, $body, 1);
    			if ($this->_showreport) {
    				$lines = array_map('trim', (array) explode("\n", $javascript_declaration));
    				plgJQueryEasyHelper::report($this->_verbose_array, 'added', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_ADDEDSCRIPTDECLARATION', $lines[0]);
    			}
    		}

    		if (!empty($this->_supplement_stylesheets)) {
    			foreach($this->_supplement_stylesheets as $path) {
    				$body = preg_replace('#([\\/a-zA-Z0-9_:\.~-]*)ADD_STYLESHEET_HERE#', $path, $body, 1);
    				plgJQueryEasyHelper::report($this->_verbose_array, 'added', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_ADDEDSTYLESHEET', $path);
    			}
    		}

    		$css_declaration = trim( (string) $this->params->get('addcssdeclaration' . $this->_suffix, ''));
    		if (!empty($css_declaration)) {
    			$body = preg_replace('#ADD_STYLESHEET_DECLARATION_HERE#', $css_declaration, $body, 1);
    			if ($this->_showreport) {
    				$lines = array_map('trim', (array) explode("\n", $css_declaration));
    				plgJQueryEasyHelper::report($this->_verbose_array, 'added', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_ADDEDSTYLESHEETDECLARATION', $lines[0]);
    			}
    		}

    		// Remove blank lines
    		// gets all of the empty lines in the source and replaces them with a simple carriage return to preserve the content structure

    		if ($this->params->get('removeblanklines' . $this->_suffix, 0)) {

    		    $number_removed = plgJQueryEasyHelper::search_and_replace('(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+', $body, "\n");
    			
    			if ($number_removed > 0) {
    			    plgJQueryEasyHelper::report($this->_verbose_array, 'deleted', 'PLG_SYSTEM_JQUERYEASY_VERBOSE_REMOVEBLANKLINES', $number_removed);
    			}
    		}
    		
    		switch ((int)$this->params->get('pagescan', 0))
    		{
    		    case 1: // head only
    		        
    		        JResponse::setBody(preg_replace('#<head>([\s\S]*)<\/head>#', $body, JResponse::getBody(), 1));
    		        
    		        break;
    		        
    		    default: // whole page scan
    		        
    		        JResponse::setBody($body);
    		}
    		
		} // END if changes to the whole page

		//JResponse::setBody($body);
		
		$time_end = microtime(true);
		$this->_timeafterrender = $time_end - $time_start;

		// show the report

		if ($this->_showreport) {
		    
		    $showreport = $this->params->get('showreport', 0);
		    
		    $this_show_in_modal = true;
		    if ($showreport == 3 || $showreport == 4) {
		        $this_show_in_modal = false;
		    }
		    
		    $report = plgJQueryEasyHelper::getReport($this->_verbose_array, $this->_timeafterroute + $this->_timebeforerender + $this->_timebeforecompilehead + $this->_timeafterrender, '', $this_show_in_modal);
		    
		    JResponse::setBody(preg_replace('#</body>#', $report.'</body>', JResponse::getBody(), 1));
		}

		return true;
	}

}