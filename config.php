<?php if (!defined('ABSPATH')) exit;

function lazyload_slideshow_get_available_effects($force_reload=false)
{
	$available_effects = array();

	$effects = get_option(Lazyload_Slideshow_Config_Effect);
	$need_handle = FALSE;
	$need_save = FALSE;

	// if effects is empty, reload effects
	if (empty($effects) || $force_reload) {
		$need_handle = TRUE;
		$need_save = TRUE;

		$files = scandir(Lazyload_Slideshow_Effect_Path);

		if (empty($files)) {
			return FALSE;
		}

		foreach ($files as $file) {
			if ($file != '.' && $file != '..') {
				$bootstrap = Lazyload_Slideshow_Effect_Path . $file . '/bootstrap.php';
				if (is_file($bootstrap)) {
					require_once $bootstrap;
				}
			}
		}
	} else {
		$available_effects = $effects;
	}

	// if no effects, return FALSE
	if (empty($available_effects)) {
		return FALSE;
	}

	// handle available_effects if necessary
	if ($need_handle) {
		$available_effects_tmp = array();
		foreach ($available_effects as $effect_name=>&$effect) {
			if (empty($effect['adapters'])) {
				continue;
			}
			if (empty($effect['name'])) {
				continue;
			}
			$effect['name_key'] = md5($effect['name']);
			if (empty($effect['folder_name'])) {
				$effect['folder_name'] = $effect['name'];
			}
			$effect['path'] = Lazyload_Slideshow_Effect_Path . $effect['folder_name'] . '/';
			if (! is_dir($effect['path'])) {
				continue;
			}
			$adapters_tmp = array();
			foreach ($effect['adapters'] as $adapter_name=>&$adapter) {
				if (empty($adapter['name'])) {
					continue;
				}
				if (empty($adapter['file_name'])) {
					$adapter['file_name'] = $adapter['name'];
				}
				$adapter['path'] = $effect['path'] . $adapter['file_name'] . '.php';
				if (! is_file($adapter['path'])) {
					continue;
				}
				$adapters_tmp[$adapter_name] = $adapter;
			}
			if (empty($adapters_tmp)) {
				continue;
			}
			$effect['adapters'] = $adapters_tmp;
			$available_effects_tmp[$effect_name] = $effect;
		}
		if (empty($available_effects_tmp)) {
			return FALSE;
		}
		$available_effects = $available_effects_tmp;
	}

	if ($need_save) {
		update_option(Lazyload_Slideshow_Config_Effect, $available_effects);
	}

	return $available_effects;
}

function lazyload_slideshow_get_config()
{
	$config = get_option(Lazyload_Slideshow_Config_Content);
	$need_save = FALSE;

	$config_default = array(
		'plugin_version' => Lazyload_Slideshow_Version,

		"lazyload" => "1",
		"lazyload_all" => "1",
		'lazyload_image_strict_match' => '0',

		"effect" => '',
		"add_effect_selector" => Lazyload_Slideshow_Content_Images_Selector,
		'effect_image_strict_match' => '1',

		"html" => "",

		"use_footer_or_head" => "wp_footer",
	);

	if (empty($config)) {
		$need_save = TRUE;

		$config = $config_default;
	} else {
		if (empty($config['plugin_version'])) { // < 3.0
			$need_save = TRUE;

			$config_tmp = $config_default;
			if ( !empty($config['use_footer_or_head']) ) {
				$config_tmp['use_footer_or_head'] = $config['use_footer_or_head'];
			}
			if ( isset($config['lazyload']) ) {
				$config_tmp['lazyload'] = $config['lazyload']?'1':'0';
			}
			if ( !empty($config['add_effect_selector']) ) {
				$config_tmp['add_effect_selector'] = $config['add_effect_selector'];
			}
			if ( !empty($config['css']) || !empty($config['tracking_code']) ) {
				$config_tmp['html'] = $config['css'] . "\n\n" . $config['tracking_code'];
			}

			$config = $config_tmp;
		} elseif ($config['plugin_version'] != Lazyload_Slideshow_Version) {
			if (Lazyload_Slideshow_Version == '3.3') {
				lazyload_slideshow_get_available_effects(true);

				if ($config['effect'] == 'prettyPhoto 3.1.4') {
					$config['effect'] = 'prettyPhoto 3.1.6';
					$config[md5('prettyPhoto 3.1.6').'-adapter'] = $config[md5('prettyPhoto 3.1.4').'-adapter'];
				}
			}
			$config['plugin_version'] = Lazyload_Slideshow_Version;
			$need_save = TRUE;
		}
	}

	if ($need_save) {
		update_option(Lazyload_Slideshow_Config_Content, $config);
	}

	return $config;
}
