<?php if (!defined('ABSPATH')) exit;
/*
Plugin Name: Images Lazyload and Slideshow
Plugin URI: http://www.brunoxu.com/images-lazyload-and-slideshow.html
Description: Lazy load all images. Add lightbox effect, gallery slideshow effect to custom selected images. Custom html.
Author: Bruno Xu
Author URI: http://www.brunoxu.com/
Version: 3.3
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

define('Lazyload_Slideshow_Version', '3.3');
define('Lazyload_Slideshow_Name', 'Images Lazyload and Slideshow');
define('Lazyload_Slideshow_Config_Effect', "lazyload_slideshow_effects");
define('Lazyload_Slideshow_Config_Content', "lazyload_slideshow_config");

$plugin_path = dirname(__FILE__).'/';
$effect_path = $plugin_path.'effects/';
$tmp_path = $plugin_path.'tmp/';
define('Lazyload_Slideshow_Plugin_Path', $plugin_path);
define('Lazyload_Slideshow_Effect_Path', $effect_path);
define('Lazyload_Slideshow_Tmp_Path', $tmp_path);

function lazyload_slideshow_get_url($path='')
{
	return plugins_url(ltrim($path, '/'), __FILE__);
}
$plugin_url = lazyload_slideshow_get_url().'/';
$effect_url = $plugin_url.'effects/';
define('Lazyload_Slideshow_Plugin_Url', $plugin_url);
define('Lazyload_Slideshow_Effect_Url', $effect_url);

define('Lazyload_Slideshow_Default_Effect', "highslide");
define('Lazyload_Slideshow_Content_Images_Selector', "#content img,.content img,.archive img,.post img,.page img");


require_once Lazyload_Slideshow_Plugin_Path.'config.php';

if (is_admin()) {
	$plugin_basename = plugin_basename(__FILE__);
	$main_entrance = __FILE__;
	require_once Lazyload_Slideshow_Plugin_Path.'admin.php';
}

if ( !is_admin() && !in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php')) ) {
	require_once Lazyload_Slideshow_Plugin_Path.'front.php';
}
