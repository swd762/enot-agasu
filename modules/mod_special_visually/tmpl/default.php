<?php
/* Запрет доступа */
defined('_JEXEC') or die;

JHtml::_('jquery.framework');

switch($params->get('change_saver')) {
	case 0: {
		$app = JFactory::getApplication();
		$jinput = $app->input;
		$jcookie = $jinput->cookie;
		$perem = 'jcookie';
		break;
	}
	case 1: {
		$session = JFactory::getSession();
		$perem = 'session';
		break;
	}
}

$doc = JFactory::getDocument();
$doc->addStyleSheet(JURI::root() . 'modules/mod_special_visually/assets/css/style.css?v0.4.1');
if ($params->get('change_sound') == 1 && $params->get('sound_api') != '' && $$perem->get('type_version') && $$perem->get('type_version') == 'yes') {
	$doc->addScript('https://code.responsivevoice.org/responsivevoice.js?key=' . $params->get('sound_api'));
}
$doc->addScript(JURI::root() . 'modules/mod_special_visually/assets/js/jQuery.style.js?v0.4.1');
$doc->addScript(JURI::root() . 'modules/mod_special_visually/assets/js/script.js?v0.4.1');

$script_before = '
	if (!window.jQuery) {
		alert("' . JText::_('MOD_SPECIAL_VISUALLY_NOT_JQUERY') . '");
	}
';
$doc->addScriptDeclaration($script_before);

if ($params->get('start_active') == '1' && empty($$perem->get('type_version'))) {
	$$perem->set('type_version', 'yes');
}

if (isset($_POST['type_version'])) {
	$$perem->set('type_version', $_POST['type_version']);
}

if (isset($_POST['change_font'])) {
	$$perem->set('change_font', $_POST['change_font']);
}

if (isset($_POST['change_color'])) {
	$$perem->set('change_color', $_POST['change_color']);
}

if (isset($_POST['change_image'])) {
	$$perem->set('change_image', $_POST['change_image']);
}

if (isset($_POST['grayscale_image'])) {
	$$perem->set('grayscale_image', $_POST['grayscale_image']);
}

if (isset($_POST['change_object'])) {
	$$perem->set('change_object', $_POST['change_object']);
}

if (isset($_POST['change_kerning'])) {
	$$perem->set('change_kerning', $_POST['change_kerning']);
}

if (isset($_POST['change_interval'])) {
	$$perem->set('change_interval', $_POST['change_interval']);
}

if (isset($_POST['change_garnitura'])) {
	$$perem->set('change_garnitura', $_POST['change_garnitura']);
}

if (isset($_POST['change_sound'])) {
	$$perem->set('change_sound', $_POST['change_sound']);
}

if (isset($_POST['reset']) && $_POST['reset'] == 'reset') {
	if ($$perem->get('change_font')) {
		$$perem->set('change_font', null);
	}
	if ($$perem->get('change_color')) {
		$$perem->set('change_color', null);
	}
	if ($$perem->get('change_image')) {
		$$perem->set('change_image', null);
	}
	if ($$perem->get('grayscale_image')) {
		$$perem->set('grayscale_image', null);
	}
	if ($$perem->get('change_object')) {
		$$perem->set('change_object', null);
	}
	if ($$perem->get('change_kerning')) {
		$$perem->set('change_kerning', null);
	}
	if ($$perem->get('change_interval')) {
		$$perem->set('change_interval', null);
	}
	if ($$perem->get('change_garnitura')) {
		$$perem->set('change_garnitura', null);
	}
}

if ($params->get('custom_button') == '1' && $params->get('custom_button_selector') != '') {
	$script = '
		jQuery(function($) {
			$("' . $params->get('custom_button_selector') . '").on("click", function(e) {
				e.preventDefault();
				$(".module_special_visually #special_visually input[name=\"type_version\"]").attr("checked", true);
				$(".module_special_visually #special_visually").submit();
				
			});
		});
	';
	$doc->addScriptDeclaration($script);
}

$style = '';
$script = '';

if ($$perem->get('type_version') && $$perem->get('type_version') == 'yes') {
	if ($$perem->get('change_font')) {
		$style .= 'font-size: ' . $$perem->get('change_font') . '!important; ';
	} else {
		$style .= 'font-size: ' . $params->get('font_default') . ' !important; ';
	}
	
	if ($$perem->get('change_color')) {
		$color = explode(';', $$perem->get('change_color'));
		$style .= 'background: ' . $color[0] . '!important; ';
		$style .= 'color: ' . $color[1] . '!important; ';
		$style .= 'border-color: ' . $color[1] . '!important; ';
	} else {
		$style .= 'background: ' . $params->get('bg_color_default') . ' !important; ';
		$style .= 'color: ' . $params->get('text_color_default') . ' !important; ';
		$style .= 'border-color: ' . $params->get('text_color_default') . ' !important; ';
	}
	
	if ($$perem->get('change_kerning')) {
		$style .= 'letter-spacing: ' . $$perem->get('change_kerning') . '!important; ';
	} else {
		$style .= 'letter-spacing: ' . $params->get('kerning_default') . ' !important; ';
	}
	
	if ($$perem->get('change_interval')) {
		$style .= 'line-height: ' . $$perem->get('change_interval') . '!important; ';
	} else {
		$style .= 'line-height: ' . $params->get('interval_default') . ' !important; ';
	}
	
	if ($$perem->get('change_garnitura')) {
		$style .= 'font-family: ' . $$perem->get('change_garnitura') . ' !important; ';
	} else {
		$style .= 'font-family: ' . $params->get('garnitura_default') . ' !important; ';
	}
	
	$not_elements = '*:not(.handle_module)';
	if ($params->get('exclude_elements') != '') {
		$exclude_elements = explode(',', $params->get('exclude_elements'));
		if (count($exclude_elements) > 0) {
			foreach($exclude_elements AS $e_e) {
				if (trim($e_e) != '') {
					$not_elements .= ':not(' . trim($e_e) . ')';
				}
			}
			unset($e_e);
		}
	}
	if ($params->get('process_image') != '') {
		$process_image = explode(',', $params->get('process_image'));
		if (count($process_image) > 0) {
			foreach($process_image AS $p_i) {
				if (trim($p_i) != '') {
					$not_elements .= ':not(' . trim($p_i) . ')';
				}
			}
			unset($p_i);
		}
	}
	
	$not_selector = '';
	$parents_selector = '';
	if ($params->get('exclude_elements') != '' && $params->get('process_image') != '') {
		$not_selector .= ':not(' . $params->get('exclude_elements') . ', ' . $params->get('process_image') . ')';
		$parents_selector .= ' || $(this).parents().is("' . $params->get('exclude_elements') . ', ' . $params->get('process_image') . '")';
	} else {
		if ($params->get('exclude_elements') != '') {
			$not_selector .= ':not(' . $params->get('exclude_elements') . ')';
			$parents_selector .= ' || $(this).parents().is("' . $params->get('exclude_elements') . '")';
		}
		if ($params->get('process_image') != '') {
			$not_selector .= ':not(' . $params->get('process_image') . ')';
			$parents_selector .= ' || $(this).parents().is("' . $params->get('process_image') . '")';
		}
	}
	
	$style = $not_elements . ' {'
		. $style
	. '}';
	
	if ($$perem->get('change_image') && $$perem->get('change_image') == 'no') {
		$style .= ' img:not(.button_icon)';
		if (isset($process_image) && count($process_image) > 0) {
			foreach($process_image AS $p_i) {
				if (trim($p_i) != '') {
					$style .= ', ' . trim($p_i);
				}
			}
			unset($p_i);
		}
		$style .= ' { display: none!important; }';
	}
	
	if (
		$params->get('grayscale_image') == 1
		|| ($params->get('grayscale_image') == 2 && $$perem->get('grayscale_image') && $$perem->get('grayscale_image') == 'bw')
		|| ($params->get('grayscale_image') == 2 && !$$perem->get('grayscale_image') && $params->get('grayscale_image_default') == 'bw')
	) {
		$style .= ' img {
			-webkit-filter: grayscale(100%);
			-moz-filter: grayscale(100%);
			-ms-filter: grayscale(100%);
			-o-filter: grayscale(100%);
			filter: grayscale(100%);
			filter: grayscale(1); /* Firefox 4+ */
			filter: gray; /* IE 6-9 */
		}';
		
		if ($params->get('process_image') != '') {
			$style .= ' ' . $params->get('process_image') . ' {
				-webkit-filter: grayscale(100%);
				-moz-filter: grayscale(100%);
				-ms-filter: grayscale(100%);
				-o-filter: grayscale(100%);
				filter: grayscale(100%);
				filter: grayscale(1); /* Firefox 4+ */
				filter: gray; /* IE 6-9 */
			}';
		}
	}
	
	if ($$perem->get('change_object') && $$perem->get('change_object') == 'no') {
		$style .= ' audio, video, iframe, object, canvas { display: none!important; }';
	}
	
	if ($params->get('hidden_elements') != '') {
		$style .= ' ' . $params->get('hidden_elements') . ' { display: none!important; }';
	}
	
	if ($params->get('custom_button') == '1' && $params->get('custom_button_selector') != '') {
		$style .= ' ' . $params->get('custom_button_selector') . ' { display: none!important; }';
	}
	
	if ($params->get('allow_underline') == '1') {
		$style .= ' a { text-decoration: underline !important; }';
	}
	
	$script = '
		jQuery(function($) {
			$("body *' . $not_selector . '").each(function() {
				if ($(this).hasClass("module_special_visually") || $(this).closest("#module_special_visually").hasClass("module_special_visually")' . $parents_selector . ') {
					
				} else {
					$(this).style("font-size", "inherit", "important");
					$(this).style("background", "inherit", "important");
					$(this).style("color", "inherit", "important");
					$(this).style("border-color", "inherit", "important");
					$(this).style("letter-spacing", "inherit", "important");
					$(this).style("line-height", "inherit", "important");
					$(this).style("font-family", "inherit", "important");
				}
			});
		});
	';
	
	if ($params->get('custom_styles') != '') {
		$style.= ' ' . $params->get('custom_styles');
	}
	
	$doc->addStyleDeclaration($style);
	$doc->addScriptDeclaration($script);
	
	if ($params->get('change_sound') == '1' && $$perem->get('change_sound') && $$perem->get('change_sound') == 'yes') {
		$voice_pitch = ($params->get('height_speaker') && $params->get('height_speaker') >= 0 && $params->get('height_speaker') <= 2) ? $params->get('height_speaker') : 1;
		$voice_speed = ($params->get('speed_speaker') && $params->get('speed_speaker') >= 0 && $params->get('speed_speaker') <= 1.5) ? $params->get('speed_speaker') : 1;
		$voice_volume = ($params->get('volume_speaker') && $params->get('volume_speaker') >= 0 && $params->get('volume_speaker') <= 1) ? $params->get('volume_speaker') : 1;
		
		if (empty($params->get('sound_method')) || $params->get('sound_method') == '0') {
			$script_method = '
				$("h1, h2, ul, p").each(function(e) {
					if ($.trim($(this).text()) != "")
						$(this).append("<span class=\"spec_vis_tts_wrapper_inner\"><span class=\"spec_vis_tts_voicer play handle_module\"></span><span class=\"spec_vis_tts_voicer stop handle_module\"></span></span>");
				});
			';
		} else {
			$script_method = '
				$("h1, h2, ul, p").each(function(e) {
					if ($.trim($(this).text()) != "") {
						$(this).addClass("spec_vis_tts_wrapper");
						$(this).append("<span class=\"spec_vis_tts_wrapper_inner\"><span class=\"spec_vis_tts_voicer play handle_module\">Воспроизвести</span><span class=\"spec_vis_tts_voicer stop handle_module\">Стоп</span></span>");
					}
				});
			';
		}
		
		$script = '
			jQuery(function($) {
				if (responsiveVoice.voiceSupport()) {
					' . $script_method . '
					responsiveVoice.setDefaultVoice("' . $params->get('change_speaker') . '");
					$(document).on("click", "span.spec_vis_tts_voicer.play", function() {
						let curButton = $(this);
						let tts_text = $(this).parent().parent().clone();
						tts_text.find(".spec_vis_tts_wrapper_inner").remove();
						tts_text = tts_text.text();
						if ($.trim(tts_text) != "") {
							if (responsiveVoice.isPlaying())
								responsiveVoice.cancel();
							responsiveVoice.speak(tts_text, "' . $params->get('change_speaker') . '", {pitch: ' . $voice_pitch . ', rate: ' . $voice_speed . ', volume: ' . $voice_volume . '});
						}
					});
					$(document).on("click", "span.spec_vis_tts_voicer.stop", function() {
						if (responsiveVoice.isPlaying())
							responsiveVoice.cancel();
					});
				}
			});
		';
		$doc->addScriptDeclaration($script);
	}
}
?>
<div id="module_special_visually" class="module_special_visually handle_module <?php echo $params->get('type_orientation'); ?> <?php echo $params->get('module_position'); ?> <?php if ($$perem->get('type_version') && $$perem->get('type_version') == 'yes') { echo 'active'; } ?> <?php echo $params->get('moduleclass_sfx'); ?>">
	<form id="special_visually" action="" method="POST">
		<?php if ($$perem->get('type_version') && $$perem->get('type_version') == 'yes') { // Версия для слабовидящих ?>
			<div class="buttons handle_module">
				<input
					id="button_type_version"
					type="radio"
					name="type_version"
					value="no"
				/>
				<label
					for="button_type_version"
					class="handle_module"
				><?php
					if ($params->get('type_original_button') == 1) {
						if ($params->get('image_original') != '') {
							echo '<img src="' . $params->get('image_original') . '" class="button_icon handle_module" />';
						} else {
							echo '<img src="' . JURI::root() . 'modules/mod_special_visually/assets/images/icon-eye-off.svg" class="button_icon handle_module" />';
						}
					} else {
						echo $params->get('text_original');
					}
				?></label>
			</div>
			<div class="params show">
				<?php
					// Функционал выбора размера шрифта
					if ($params->get('change_font') == '1') {
						$list_font = json_decode($params->get('list_font'), true);
						if (isset($list_font) && is_array($list_font) && count($list_font)) {
				?>
							<div class="change_font param handle_module">
								<div class="title handle_module"><?php echo JText::_('MOD_SPECIAL_VISUALLY_CHANGE_FONT_FRONT'); ?></div>
								<div class="values">
									<?php
										$i = 0;
										foreach($list_font as $size_font) {
											foreach($size_font as $size) {
												$i++;
									?>
												<div class="value">
													<input
														id="change_font_<?php echo $i; ?>"
														type="radio"
														name="change_font"
														<?php echo ($$perem->get('change_font') && $$perem->get('change_font') == $size) ? 'checked="checked"' : ''; ?>
														value="<?php echo $size; ?>"
													/>
													<label
														for="change_font_<?php echo $i; ?>"
														class="handle_module <?php echo ($$perem->get('change_font') && $$perem->get('change_font') == $size) ? 'active' : ''; ?>"
														style="font-size: <?php echo $size; ?>!important;"
													>A</label>
												</div>
									<?php
											}
										}
									?>
								</div>
							</div>
				<?php
						} else {
							echo '<p>' . JText::_('MOD_SPECIAL_VISUALLY_CHANGE_FONT_FRONT_NOT_LIST') . '</p>';
						}
					}
					
					// Функционал выбора цветовой схемы
					if ($params->get('change_color') == '1') {
						$list_color = json_decode($params->get('list_color'), true);
						if (isset($list_color) && is_array($list_color) && count($list_color)) {
							foreach ($list_color as $colors) {
								foreach ($colors as $key => $value) {
									$result_colors[$key][] = $value;
								}
							}
							if (count($result_colors)) {
				?>
								<div class="change_color param handle_module">
									<div class="title handle_module"><?php echo JText::_('MOD_SPECIAL_VISUALLY_CHANGE_COLOR_FRONT'); ?></div>
									<div class="values">
										<?php
											$i = 0;
											foreach ($result_colors as $colors) {
												$i++;
										?>
												<div class="value">
													<input
														id="change_color_<?php echo $i; ?>"
														type="radio"
														name="change_color"
														<?php echo ($$perem->get('change_color') && $$perem->get('change_color') == ($colors[0] . ';' . $colors[1])) ? 'checked="checked"' : ''; ?>
														value="<?php echo $colors[0] . ';' . $colors[1]; ?>"
													/>
													<label
														for="change_color_<?php echo $i; ?>"
														class="handle_module <?php echo ($$perem->get('change_color') && $$perem->get('change_color') == ($colors[0] . ';' . $colors[1])) ? 'active' : ''; ?>"
														style="background-color: <?php echo $colors[0]; ?>!important; color: <?php echo $colors[1]; ?>!important;"
													>Ц</label>
												</div>
										<?php
											}
										?>
									</div>
								</div>
				<?php
							} else {
								echo '<p>' . JText::_('MOD_SPECIAL_VISUALLY_CHANGE_COLOR_FRONT_NOT_LIST') . '</p>';
							}
						} else {
							echo '<p>' . JText::_('MOD_SPECIAL_VISUALLY_CHANGE_COLOR_FRONT_NOT_LIST') . '</p>';
						}
					}
					
					// Функционал управления изображениями и Функционал обесцвечивания изображений
					if ($params->get('change_image') == '1' || $params->get('grayscale_image') == '2') {
				?>
						<div class="change_image param handle_module">
							<div class="title handle_module"><?php echo JText::_('MOD_SPECIAL_VISUALLY_CHANGE_IMAGE_FRONT'); ?></div>
							<div class="values">
								<?php if ($params->get('change_image') == '1') { ?>
									<div class="value">
										<input
											id="change_image_yes"
											type="radio"
											name="change_image"
											<?php echo (($$perem->get('change_image') && $$perem->get('change_image') == 'yes') || !$$perem->get('change_image')) ? 'checked="checked"' : ''; ?>
											value="yes"
										/>
										<label
											for="change_image_yes"
											class="handle_module <?php echo (($$perem->get('change_image') && $$perem->get('change_image') == 'yes') || !$$perem->get('change_image')) ? 'active' : ''; ?>"
										><img class="button_icon handle_module" src="<?php echo JURI::root(); ?>modules/mod_special_visually/assets/images/icon-img-on.svg" alt="<?php echo JText::_('MOD_SPECIAL_VISUALLY_CHANGE_IMAGE_ON'); ?>" /></label>
									</div>
									<div class="value">
										<input
											id="change_image_no"
											type="radio"
											name="change_image"
											<?php echo ($$perem->get('change_image') && $$perem->get('change_image') == 'no') ? 'checked="checked"' : ''; ?>
											value="no"
										/>
										<label
											for="change_image_no"
											class="handle_module <?php echo ($$perem->get('change_image') && $$perem->get('change_image') == 'no') ? 'active' : ''; ?>"
										><img class="button_icon handle_module" src="<?php echo JURI::root(); ?>modules/mod_special_visually/assets/images/icon-img-off.svg" alt="<?php echo JText::_('MOD_SPECIAL_VISUALLY_CHANGE_IMAGE_OFF'); ?>" /></label>
									</div>
								<?php } ?>
								
								<?php if ($params->get('grayscale_image') == '2') { ?>
									<div class="value">
										<input
											id="grayscale_image"
											type="checkbox"
											name="grayscale_image"
											value="<?php echo (($$perem->get('grayscale_image') && $$perem->get('grayscale_image') == 'cr') || (!$$perem->get('grayscale_image') && $params->get('grayscale_image_default') == 'cr')) ? 'bw' : 'cr'; ?>"
										/>
										<label
											for="grayscale_image"
											class="handle_module <?php echo (($$perem->get('grayscale_image') && $$perem->get('grayscale_image') == 'bw') || (!$$perem->get('grayscale_image') && $params->get('grayscale_image_default') == 'bw')) ? 'active' : ''; ?>"
										><img class="button_icon handle_module" src="<?php echo JURI::root(); ?>modules/mod_special_visually/assets/images/icon-contrast-bw.svg" alt="<?php echo JText::_('MOD_SPECIAL_VISUALLY_GRAYSCALE_IMAGE_DEFAULT_BW'); ?>" /></label>
									</div>
								<?php } ?>
							</div>
						</div>
				<?php
					}
					
					// Функционал управления объектами
					if ($params->get('change_object') == '1') {
				?>
						<div class="change_object param handle_module">
							<div class="title handle_module"><?php echo JText::_('MOD_SPECIAL_VISUALLY_CHANGE_OBJECT'); ?></div>
							<div class="values">
								<div class="value">
									<input
										id="change_object_on"
										type="radio"
										name="change_object"
										<?php echo (empty($$perem->get('change_object')) || $$perem->get('change_object') == 'yes') ? 'checked="checked"' : ''; ?>
										value="yes"
									/>
									<label
										for="change_object_on"
										class="handle_module <?php echo (empty($$perem->get('change_object')) || $$perem->get('change_object') == 'yes') ? 'active' : ''; ?>"
									><img class="button_icon handle_module" src="<?php echo JURI::root(); ?>modules/mod_special_visually/assets/images/icon-object-on.svg" /></label>
								</div>
								<div class="value">
									<input
										id="change_object_off"
										type="radio"
										name="change_object"
										<?php echo ($$perem->get('change_object') && $$perem->get('change_object') == 'no') ? 'checked="checked"' : ''; ?>
										value="no"
									/>
									<label
										for="change_object_off"
										class="handle_module <?php echo ($$perem->get('change_object') && $$perem->get('change_object') == 'no') ? 'active' : ''; ?>"
									><img class="button_icon handle_module" src="<?php echo JURI::root(); ?>modules/mod_special_visually/assets/images/icon-object-off.svg" /></label>
								</div>
							</div>
						</div>
				<?php
					}
					
					// Функционал выбора кернинга
					if ($params->get('change_kerning') == '1') {
						$list_kerning = json_decode($params->get('list_kerning'), true);
						if (isset($list_kerning) && is_array($list_kerning) && count($list_kerning)) {
							foreach ($list_kerning as $kernings) {
								foreach ($kernings as $key => $value) {
									$result_kernings[$key][] = $value;
								}
							}
							if (count($result_kernings)) {
				?>
								<div class="change_kerning param handle_module">
									<div class="title handle_module"><?php echo JText::_('MOD_SPECIAL_VISUALLY_CHANGE_KERNING_FRONT'); ?></div>
									<div class="values">
										<?php
											$i = 0;
											foreach ($result_kernings as $kernings) {
												$i++;
										?>
												<div class="value">
													<input
														id="change_kerning_<?php echo $i; ?>"
														type="radio"
														name="change_kerning"
														<?php echo ($$perem->get('change_kerning') && $$perem->get('change_kerning') == $kernings[2]) ? 'checked="checked"' : ''; ?>
														value="<?php echo $kernings[2]; ?>"
													/>
													<label
														for="change_kerning_<?php echo $i; ?>"
														class="handle_module <?php echo ($$perem->get('change_kerning') && $$perem->get('change_kerning') == $kernings[2]) ? 'active' : ''; ?>"
													><?php echo ($params->get('type_kerning') == 1 && $kernings[1] != '') ? '<img src="' . $kernings[1] . '" class="button_icon handle_module" title="' . $kernings[0] . '" />' : $kernings[0]; ?></label>
												</div>
										<?php
											}
										?>
									</div>
								</div>
				<?php
							} else {
								echo '<p>' . JText::_('MOD_SPECIAL_VISUALLY_CHANGE_KERNING_FRONT_NOT_LIST') . '</p>';
							}
						} else {
							echo '<p>' . JText::_('MOD_SPECIAL_VISUALLY_CHANGE_KERNING_FRONT_NOT_LIST') . '</p>';
						}
					}
					
					// Функционал выбора интервала
					if ($params->get('change_interval') == '1') {
						$list_interval = json_decode($params->get('list_interval'), true);
						if (isset($list_interval) && is_array($list_interval) && count($list_interval)) {
							foreach ($list_interval as $intervals) {
								foreach ($intervals as $key => $value) {
									$result_intervals[$key][] = $value;
								}
							}
							if (count($result_intervals)) {
				?>
								<div class="change_interval param handle_module">
									<div class="title handle_module"><?php echo JText::_('MOD_SPECIAL_VISUALLY_CHANGE_INTERVAL_FRONT'); ?></div>
									<div class="values">
										<?php
											$i = 0;
											foreach ($result_intervals as $intervals) {
												$i++;
										?>
												<div class="value">
													<input
														id="change_interval_<?php echo $i; ?>"
														type="radio"
														name="change_interval"
														<?php echo ($$perem->get('change_interval') && $$perem->get('change_interval') == $intervals[2]) ? 'checked="checked"' : ''; ?>
														value="<?php echo $intervals[2]; ?>"
													/>
													<label
														for="change_interval_<?php echo $i; ?>"
														class="handle_module <?php echo ($$perem->get('change_interval') && $$perem->get('change_interval') == $intervals[2]) ? 'active' : ''; ?>"
													><?php echo ($params->get('type_interval') == 1 && $intervals[1] != '') ? '<img src="' . $intervals[1] . '" class="button_icon handle_module" title="' . $intervals[0] . '" />' : $intervals[0]; ?></label>
												</div>
										<?php
											}
										?>
									</div>
								</div>
				<?php
							} else {
								echo '<p>' . JText::_('MOD_SPECIAL_VISUALLY_CHANGE_INTERVAL_FRONT_NOT_LIST') . '</p>';
							}
						} else {
							echo '<p>' . JText::_('MOD_SPECIAL_VISUALLY_CHANGE_INTERVAL_FRONT_NOT_LIST') . '</p>';
						}
					}
					
					// Функционал выбора гарнитуры
					if ($params->get('change_garnitura') == '1') {
						$list_garnitura = json_decode($params->get('list_garnitura'), true);
						if (isset($list_garnitura) && is_array($list_garnitura) && count($list_garnitura)) {
							foreach ($list_garnitura as $garnituras) {
								foreach ($garnituras as $key => $value) {
									$result_garnituras[$key][] = $value;
								}
							}
							if (count($result_garnituras)) {
				?>
								<div class="change_garnitura param handle_module">
									<div class="title handle_module"><?php echo JText::_('MOD_SPECIAL_VISUALLY_CHANGE_GARNITURA_FRONT'); ?></div>
									<div class="values">
										<?php
											$i = 0;
											foreach ($result_garnituras as $garnituras) {
												$i++;
										?>
												<div class="value">
													<input
														id="change_garnitura_<?php echo $i; ?>"
														type="radio"
														name="change_garnitura"
														<?php echo ($$perem->get('change_garnitura') && $$perem->get('change_garnitura') == $garnituras[1]) ? 'checked="checked"' : ''; ?>
														value="<?php echo $garnituras[1]; ?>"
													/>
													<label
														for="change_garnitura_<?php echo $i; ?>"
														class="handle_module <?php echo ($$perem->get('change_garnitura') && $$perem->get('change_garnitura') == $garnituras[1]) ? 'active' : ''; ?>"
													><?php echo $garnituras[0]; ?></label>
												</div>
										<?php
											}
										?>
									</div>
								</div>
				<?php
							} else {
								echo '<p>' . JText::_('MOD_SPECIAL_VISUALLY_CHANGE_GARNITURA_NOT_LIST') . '</p>';
							}
						} else {
							echo '<p>' . JText::_('MOD_SPECIAL_VISUALLY_CHANGE_GARNITURA_NOT_LIST') . '</p>';
						}
					}
					
					// Функционал синтеза речи
					if ($params->get('change_sound') == '1') {
				?>
						<div class="change_sound param handle_module">
							<div class="title handle_module"><?php echo JText::_('MOD_SPECIAL_VISUALLY_CHANGE_SOUND_FRONT'); ?></div>
							<div class="values">
								<div class="value">
									<input
										id="change_sound_on"
										type="radio"
										name="change_sound"
										<?php echo ($$perem->get('change_sound') && $$perem->get('change_sound') == 'yes') ? 'checked="checked"' : ''; ?>
										value="yes"
									/>
									<label
										for="change_sound_on"
										class="handle_module <?php echo (($$perem->get('change_sound') && $$perem->get('change_sound') == 'yes')) ? 'active' : ''; ?>"
									><img class="button_icon handle_module" src="<?php echo JURI::root(); ?>modules/mod_special_visually/assets/images/icon-sound-on.svg" alt="<?php echo JText::_('MOD_SPECIAL_VISUALLY_CHANGE_SOUND_ON'); ?>" /></label>
								</div>
								<div class="value">
									<input
										id="change_sound_off"
										type="radio"
										name="change_sound"
										<?php echo (empty($$perem->get('change_sound')) || $$perem->get('change_sound') == 'no') ? 'checked="checked"' : ''; ?>
										value="no"
									/>
									<label
										for="change_sound_off"
										class="handle_module <?php echo (empty($$perem->get('change_sound')) || $$perem->get('change_sound') == 'no') ? 'active' : ''; ?>"
									><img class="button_icon handle_module" src="<?php echo JURI::root(); ?>modules/mod_special_visually/assets/images/icon-sound-off.svg" alt="<?php echo JText::_('MOD_SPECIAL_VISUALLY_CHANGE_SOUND_OFF'); ?>" /></label>
								</div>
							</div>
						</div>
				<?php
					}
					
					// Кнопка сброса
					if ($params->get('type_reset_button') != 0) {
				?>
						<div class="buttons handle_module">
							<input
								id="button_reset"
								type="radio"
								name="reset"
								value="reset"
							/>
							<label
								for="button_reset"
								class="handle_module"
							><?php echo ($params->get('type_reset_button') == 2 && $params->get('image_reset') != '') ? '<img src="' . $params->get('image_reset') . '" class="button_icon handle_module" />' : $params->get('text_reset'); ?></label>
						</div>
				<?php } ?>
			</div>
			<div class="close_special_block handle_module"><?php echo JText::_('MOD_SPECIAL_VISUALLY_SHOW_HIDE'); ?></div>
		<?php } else { // Версия оригинальная ?>
			<div class="buttons <?php if ($params->get('custom_button') == '1' && $params->get('custom_button_selector') != '') { echo 'hidden'; } ?>">
				<input
					id="button_type_version"
					type="radio"
					name="type_version"
					value="yes"
				/>
				<label
					for="button_type_version"
				><?php
					if ($params->get('type_special_button') == 1) {
						if ($params->get('image_special') != '') {
							echo '<img src="' . $params->get('image_special') . '" class="button_icon" />';
						} else {
							echo '<img src="' . JURI::root() . 'modules/mod_special_visually/assets/images/icon-eye-on.svg" class="button_icon" />';
						}
					} else {
						echo $params->get('text_special');
					}
				?>
			</div>
		<?php } ?>
	</form>
</div>