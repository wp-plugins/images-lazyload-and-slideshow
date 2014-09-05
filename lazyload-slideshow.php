<?php
/*
Plugin Name: Images Lazyload and Slideshow
Plugin URI: http://www.brunoxu.com/images-lazyload-and-slideshow.html
Description: Lazy load all images. Add lightbox effect, gallery slideshow effect to custom selected images. Custom html.
Author: Bruno Xu
Author URI: http://www.brunoxu.com/
Version: 3.1.1
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

define('Lazyload_Slideshow_Name', 'Images Lazyload and Slideshow');
define('Lazyload_Slideshow_Version', '3.1.1');
define('Lazyload_Slideshow_Config_Effect', "lazyload_slideshow_effects");
define('Lazyload_Slideshow_Config_Content', "lazyload_slideshow_config");

$plugin_path = dirname(__FILE__).'/';
$effect_path = $plugin_path.'effects/';
$tmp_path = $plugin_path.'tmp/';
define('Lazyload_Slideshow_Plugin_Path', $plugin_path);
define('Lazyload_Slideshow_Effect_Path', $effect_path);
define('Lazyload_Slideshow_Tmp_Path', $tmp_path);

define('Lazyload_Slideshow_Default_Effect', "highslide");

$content_images_selector = "#content img,.content img,.archive img,.post img,.page img";
define('Lazyload_Slideshow_Content_Images_Selector', $content_images_selector);

function lazyload_slideshow_get_url($path='')
{
	return plugins_url(ltrim($path, '/'), __FILE__);
}
$plugin_url = lazyload_slideshow_get_url().'/';
$effect_url = $plugin_url.'effects/';
define('Lazyload_Slideshow_Plugin_Url', $plugin_url);
define('Lazyload_Slideshow_Effect_Url', $effect_url);

function lazyload_slideshow_get_available_effects()
{
	$available_effects = array();

	$effects = get_option(Lazyload_Slideshow_Config_Effect);
	$need_handle = FALSE;
	$need_save = FALSE;

	// if effects is empty, reload effects
	if (empty($effects)) {
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

	if (empty($config)) {
		$need_save = TRUE;

		$config = array(
			'plugin_version' => Lazyload_Slideshow_Version,

			"lazyload" => "1",
			"lazyload_all" => "1",
			'lazyload_image_strict_match' => '0',

			"effect" => Lazyload_Slideshow_Default_Effect,
			"add_effect_selector" => Lazyload_Slideshow_Content_Images_Selector,
			'effect_image_strict_match' => '1',

			"html" => "",

			"use_footer_or_head" => "wp_footer",
		);
	} else {
		if (empty($config['plugin_version'])) { // < 3.0
			$need_save = TRUE;

			$config_tmp = array(
				'plugin_version' => Lazyload_Slideshow_Version,
		
				"lazyload" => "1",
				"lazyload_all" => "1",
				'lazyload_image_strict_match' => '0',
		
				"effect" => Lazyload_Slideshow_Default_Effect,
				"add_effect_selector" => Lazyload_Slideshow_Content_Images_Selector,
				'effect_image_strict_match' => '1',
		
				"html" => "",

				"use_footer_or_head" => "wp_footer",
			);
			if (empty($config['use_footer_or_head'])) {
				$config_tmp['use_footer_or_head'] = $config['use_footer_or_head'];
			}
			if (isset($config['lazyload'])) {
				$config_tmp['lazyload'] = $config['lazyload']?'1':'0';
			}
			if (empty($config['add_effect_selector'])) {
				$config_tmp['add_effect_selector'] = $config['add_effect_selector'];
			}
			if ( (! empty($config['css'])) || (! empty($config['tracking_code'])) ) {
				$config_tmp['html'] = $config['css'] . "\n\n" . $config['tracking_code'];
			}

			$config = $config_tmp;
		}
	}

	if ($need_save) {
		update_option(Lazyload_Slideshow_Config_Content, $config);
	}

	return $config;
}


/** backend, add admin menu and config page */
if (is_admin()) {
	add_filter('plugin_action_links', 'lazyload_slideshow_add_settings_link', 10, 2);
	function lazyload_slideshow_add_settings_link($links, $file) {
		static $this_plugin;
		if (!$this_plugin) $this_plugin = plugin_basename(__FILE__);

		if ($file == $this_plugin){
			$settings_link = '<a href="'.wp_nonce_url("options-general.php?page=images-lazyload-and-slideshow/lazyload-slideshow.php").'">Setting</a>';
			array_unshift($links, $settings_link);
		}
		return $links;
	}

	add_action('admin_menu','lazyload_slideshow_admin_menu');
	function lazyload_slideshow_admin_menu()
	{
		add_options_page(
			Lazyload_Slideshow_Name.' Setting',
			Lazyload_Slideshow_Name,
			8,
			__FILE__,
			'lazyload_slideshow_config_page'
		);
	}

	// lazyload_slideshow config page
	function lazyload_slideshow_config_page()
	{
		$msgs = array(
			'error' => array(),
			'success' => array(),
		);

		if ($_POST) {
			if (isset($_POST['op_clear_effects'])) {
				delete_option(Lazyload_Slideshow_Config_Effect);
				$msgs['success'][] = 'Effects cleared successfully.';
			} elseif (isset($_POST['op_delete_effect'])) {
				if (empty($_POST['effect_to_delete'])) {
					$msgs['error'][] = 'Please select an effect.';
				} else {
					$effect_to_delete = $_POST['effect_to_delete'];
					$config = lazyload_slideshow_get_config();
					if ($config['effect'] == $effect_to_delete) {
						$msgs['error'][] = 'Can not delete effect in use.';
					} else {
						$effect_to_delete_path = Lazyload_Slideshow_Effect_Path . $effect_to_delete;
						if (file_exists($effect_to_delete_path)) {
							if (! function_exists('lazyload_slideshow_rmdir_recurse')) {
								function lazyload_slideshow_rmdir_recurse($dir) {
									if ($objs = glob ( $dir . "/*" )) {
										foreach($objs as $obj) {
											is_dir($obj)? lazyload_slideshow_rmdir_recurse($obj) : unlink($obj);
										}
									}
									rmdir($dir);
									return true;
								}
							}
							if (lazyload_slideshow_rmdir_recurse($effect_to_delete_path)) {
								delete_option(Lazyload_Slideshow_Config_Effect);
								$msgs['success'][] = 'Effect deleted successfully.';
							} else {
								$msgs['error'][] = 'Delete effect failed.';
							}
						} else {
							$msgs['error'][] = 'Effect path does not exist.';
						}
					}
				}
			} elseif (isset($_POST['op_upload_effects'])) {
				$upload_error = '';
				if (empty($_FILES['effect_to_add'])) {
					$upload_error = 'Please select a file.';
				} else {
					$effect_to_add = $_FILES['effect_to_add'];
					if ($effect_to_add['error']) {
						$upload_error = 'Upload failed.';
					} elseif ($effect_to_add['size'] > 2*1024*1024) {
						$upload_error = 'The uploaded file exceeds the max_filesize_limitation: 2M';
					} elseif (! stristr(substr($effect_to_add['name'], -4), '.zip')) {// or ['type']=='application/x-zip-compressed'
						$upload_error = 'Please upload file in a .zip format.';
					} else {
						/*
						$result = unzip_file($effect_to_add['tmp_name'], Lazyload_Slideshow_Effect_Path);
						if ($result instanceof WP_Error) {
							$upload_error = 'Unzip failed. ' . $result->get_error_message();
						}
						//Cannot access the file system
						*/

						/*
						$new_filename = time().'.zip';
						$new_filename_path = Lazyload_Slideshow_Tmp_Path.$new_filename;
						if (move_uploaded_file($effect_to_add['tmp_name'], $new_filename_path)) {
							WP_Filesystem();
							$result = unzip_file($new_filename_path, Lazyload_Slideshow_Effect_Path);
							if ($result instanceof WP_Error) {
								$upload_error = 'Unzip failed. ' . $result->get_error_message();
							} else {
								if (! unlink($new_filename_path)) {
									$upload_error = 'Effect installed successfully, but delete zip file failed, you can delete it via ftp.';
								}
							}
						} else {
							$upload_error = 'Move file failed.';
						}
						//the same "Cannot access the file system", after add "WP_Filesystem();", it's ok to upload, no error alert, but upload file isn't deleted, maybe cause it's opened.
						*/

						WP_Filesystem();
						$result = unzip_file($effect_to_add['tmp_name'], Lazyload_Slideshow_Effect_Path);
						if ($result instanceof WP_Error) {
							$upload_error = 'Unzip failed. ' . $result->get_error_message();
						}
					}
				}
				if ($upload_error) {
					$msgs['error'][] = $upload_error;
				} else {
					delete_option(Lazyload_Slideshow_Config_Effect);
					$msgs['success'][] = 'Effect installed successfully.';
				}
			}
		}

		global $available_effects;
		$available_effects = lazyload_slideshow_get_available_effects();
		if (! $available_effects) {
			$msgs['error'][] = 'There\'s no available effects, please re-install the plugin or leave a messaeg to the author.';
		}

		global $config;
		$config = lazyload_slideshow_get_config();
		if ($config['effect'] && empty($available_effects[$config['effect']])) {
			$msgs['error'][] = 'Effect <b>'.$config['effect'].'</b> is no longer exsit, please select a new effect for your website.';
		}

		$html_reference = '
<!-- custom styles -->
<style type="text/css">
/* set max-width for content images */
'.Lazyload_Slideshow_Content_Images_Selector.'{
margin-top:3px;max-width:600px;
height:auto !important;
_width:expression(this.width>600?600:auto);
}
/* style for slideshow images */
.ls_slideshow_imgs{cursor:url(http://simplenotestheme.googlecode.com/svn/trunk/autohighslide/highslide/graphics/zoomin.cur), pointer;}
.ls_slideshow_imgs:hover{opacity:0.5 !important;filter:alpha(opacity=50) !important;}
#fancybox-wrap{cursor:url(http://simplenotestheme.googlecode.com/svn/trunk/autohighslide/highslide/graphics/zoomout.cur), pointer;}
</style>

<!-- baidu tracking code -->
<div style="width:0;height:0;overflow:hidden;">
<script type="text/javascript">
var _bdhmProtocol = (("https:" == document.location.protocol) ? " https://" : " http://");
document.write(unescape("%3Cscript src=\'" + _bdhmProtocol + "hm.baidu.com/h.js%3Fed87e845538b0fe86a4caf1d0018e458\' type=\'text/javascript\'%3E%3C/script%3E"));
</script>
</div>
';

		if (! function_exists('lazyload_slideshow_get_post_confg')) {
			function lazyload_slideshow_get_post_confg() {
				global $available_effects,$config;
				if (empty($_POST['config'])) {
					return FALSE;
				}
				$post_config = $_POST['config'];
				$post_config['lazyload'] = isset($post_config['lazyload'])?'1':'0';
				if (! empty($post_config['effect'])) {
					//$adapter_name_key = md5($post_config['effect']).'-adapter';
					$adapter_name_key = $available_effects[$post_config['effect']]['name_key'].'-adapter';
					$post_config[$adapter_name_key] = $_POST[$adapter_name_key];
				}
				$config = array_merge($config, $post_config);
				return $config;
			}
		}

		if ($_POST) {
			if (isset($_POST['op_save_config'])) {
				$post_config = lazyload_slideshow_get_post_confg();
				if (! $post_config) {
					$msgs['error'][] = 'Post data error, not saved.';
				} else {
					update_option(Lazyload_Slideshow_Config_Content, $post_config);

					$msgs['error'] = array();
					$msgs['success'][] = 'Config saved successfully.';

					$config = get_option(Lazyload_Slideshow_Config_Content);
				}
			}
		}

		if ($msgs['error']) {
			echo '<div class="error">';
			foreach ($msgs['error'] as $m) {
				echo '<p><strong>'.$m.'</strong>';
			}
			echo '</div>';
		}
		if ($msgs['success']) {
			echo '<div class="updated">';
			foreach ($msgs['success'] as $m) {
				echo '<p><strong>'.$m.'</strong>';
			}
			echo '</div>';
		}
?>
<div class="wrap">
<style type="text/css">
.clear{clear:both}

dt,dd{padding:15px 3px 0;}
dt{float:left;width:18%;clear:both;text-align: right;line-height: 25px;}
dd{float:left;width:76%;margin:0;line-height: 25px;*float:none;*display:inline-block;}

.tips{
background-color: #FFFBCC;
border-color: #E6DB55;
color: #555;
line-height: 19px;
padding: 3px;
font-size: 12px;
border-width: 1px;
border-style: solid;
-webkit-border-radius: 3px;
border-radius: 3px;
}
.tips pre{margin:0;}

#dlAddEffectSelector input{width: 100%;}
</style>

	<h2><?php echo Lazyload_Slideshow_Name.' Setting'; ?></h2>

	<form method="post">
		<div class="clear" style="padding-top:10px;"></div>

		<p><strong>Plugin Name :</strong> <?php echo Lazyload_Slideshow_Name; ?></p>
		<p><strong>Plugin Version :</strong> <?php echo Lazyload_Slideshow_Version; ?></p>
		<p><strong>Plugin Author :</strong> <a href="http://www.brunoxu.com/" target="_blank">Bruno Xu</a> (xuguangzhi2003@126.com)</p>
		<p><strong>Plugin Homepage :</strong> <a href="http://www.brunoxu.com/images-lazyload-and-slideshow.html" target="_blank">http://www.brunoxu.com/images-lazyload-and-slideshow.html</a></p>

		<div class="clear" style="padding-top:10px;"></div>
		<hr/>

		<h4>General Configs:</h4>

		<dl>
			<dt><strong>Use Lazyload:</strong></dt>
			<dd>
				<input type="checkbox" onchange="onChangeLazyload(this)" name="config[lazyload]" value="1"<?php if($config["lazyload"]) echo ' checked="checked"'; ?> />
			</dd>
		</dl>
		<dl id="dlLazyloadAll"<?php if(! $config["lazyload"]) echo ' style="display:none;"'; ?>>
			<dt><strong>Lazyload All Images?</strong></dt>
			<dd>
				<select name="config[lazyload_all]">
					<option value="1"<?php if($config["lazyload_all"]) echo ' selected="selected"'; ?>>all images</option>
					<option value="0"<?php if(!$config["lazyload_all"]) echo ' selected="selected"'; ?>>only images in post content</option>
				</select>
			</dd>
		</dl>
		<?php /*<dl id="dlLazyloadImageStrictMatch"<?php if(! $config["lazyload"]) echo ' style="display:none;"'; ?>>
			<dt><strong>Lazyload Image Strict Match:</strong></dt>
			<dd>
				<select name="config[lazyload_image_strict_match]">
					<option value="0"<?php if(!$config["lazyload_image_strict_match"]) echo ' selected="selected"'; ?>>all images</option>
					<option value="1"<?php if($config["lazyload_image_strict_match"]) echo ' selected="selected"'; ?>>only images with 'bmp|gif|jpeg|jpg|png' suffixes</option>
				</select>
			</dd>
		</dl>*/ ?>
		<input type="hidden" name="config[lazyload_image_strict_match]" value="0" />
		<script type="text/javascript">
		function onChangeLazyload(domLazyload){
			if (domLazyload.checked) {
				document.getElementById("dlLazyloadAll").style.display = "";
				//document.getElementById("dlLazyloadImageStrictMatch").style.display = "";
			} else {
				document.getElementById("dlLazyloadAll").style.display = "none";
				//document.getElementById("dlLazyloadImageStrictMatch").style.display = "none";
			}
		}
		</script>

		<dl>
			<dt><strong>Select Slideshow Effect:</strong></dt>
			<dd>
				<select name="config[effect]" onchange="onChangeEffect(this)">
					<option value="">Do Not Use</option>
					<?php foreach($available_effects as $effect_name=>$effect): ?>
					<option value="<?php echo htmlspecialchars($effect_name); ?>"<?php if($config["effect"]==$effect_name) echo ' selected="selected"'; ?>>
					<?php echo $effect['name']; ?>
					</option>
					<?php endforeach; ?>
				</select>
				<span id="spanAdapterSelect" style="padding: 0 0 0 50px;">
					Select Adapter:
					<?php foreach($available_effects as $effect_name=>$effect): ?>
					<select id="<?php echo "select-".$effect['name_key']."-adapter"; ?>" name="<?php echo $effect['name_key']."-adapter"; ?>"<?php
							if($config["effect"]!=$effect_name) echo ' style="display:none;"'; ?>>
						<?php foreach($effect["adapters"] as $adapter_name=>$adapter): ?>
						<option value="<?php echo htmlspecialchars($adapter_name); ?>" <?php
							if( (!empty($config[$effect['name_key']."-adapter"])) && $config[$effect['name_key']."-adapter"]==$adapter_name )
								echo ' selected="selected"'; ?>>
							<?php echo $adapter['name'] . ' ' . $adapter['version']; ?>
						</option>
						<?php endforeach; ?>
					</select>
					<?php endforeach; ?>
				</span>
			</dd>
			<script type="text/javascript">
			<?php echo 'var available_effects = '.json_encode($available_effects).';'?>
			function onChangeEffect(domEffect){
				adapters = document.getElementById("spanAdapterSelect").children;
				for (var i=0;i<adapters.length;i++){
					adapters[i].style.display = "none";
				}

				selected_effect = domEffect.value;
				if (selected_effect) {
					document.getElementById("spanAdapterSelect").style.display = "";
					document.getElementById("select-"+available_effects[selected_effect]['name_key']+"-adapter").style.display = "";
				} else {
					document.getElementById("spanAdapterSelect").style.display = "none";
				}

				if (selected_effect) {
					document.getElementById("dlAddEffectSelector").style.display = "";
				} else {
					document.getElementById("dlAddEffectSelector").style.display = "none";
				}
			}
			</script>
		</dl>
		<dl id="dlAddEffectSelector"<?php if(empty($config["effect"])) echo ' style="display:none;"'; ?>>
			<dt><strong>Add Effect Selector:</strong></dt>
			<dd>
				<input type="text" name="config[add_effect_selector]"
					value="<?php echo htmlspecialchars($config["add_effect_selector"]); ?>"/>
				<div class="tips">
					<pre><?php echo "<b>Sample:</b>\n".Lazyload_Slideshow_Content_Images_Selector; ?></pre>
				</div>
			</dd>
		</dl>
		<input type="hidden" name="config[effect_image_strict_match]" value="1" />

		<dl>
			<dt><strong>Custom Html:</strong></dt>
			<dd style="width:38%">
				<textarea name="config[html]" style="width:100%;height:280px;"><?php echo stripslashes($config["html"]); ?></textarea>
			</dd>
			<div style="width:38%;height:280px;float:left;margin:15px 0 0 5px;overflow:scroll;">
				<div class="tips"><pre><?php echo "<b>Sample:</b>".htmlspecialchars($html_reference); ?></pre></div>
			</div>
		</dl>

		<dl>
			<dt><strong>Action to Hook:</strong></dt>
			<dd>
				<select name="use_footer_or_head">
					<option value="wp_footer"<?php if ($config["use_footer_or_head"]=="wp_footer") echo ' selected="selected"'; ?>>wp_footer</option>
					<option value="wp_head"<?php if ($config["use_footer_or_head"]=="wp_head") echo ' selected="selected"'; ?>>wp_head</option>
				</select>
			</dd>
		</dl>

		<div class="clear" style="padding-top:10px;"></div>

		<p class="clear" style="padding-left: 19%;">
			<input type="submit" class="button-primary" name="op_save_config" value=" Save Changes " />
		</p>
	</form>

	<form method="post" enctype="multipart/form-data" class="wp-upload-form">
		<div class="clear" style="padding-top:10px;"></div>
		<hr/>
		<?php /*<p>If you has an external effect, you can install and use it immediatly.</p>*/ ?>
		<h4>Install an effect in .zip format</h4>
		<p class="install-help">If you have an effect in a .zip format, you may install it by uploading it here.</p>
		<p>
			<input type="file" name="effect_to_add" id="effect_to_add" />
			<input type="submit" name="op_upload_effects" id="op_upload_effects" class="button" value="Install Now" disabled="" />
		</p>
		<p>Get more effects, please visit &nbsp;&nbsp;&nbsp;&nbsp; <b><a href="http://www.brunoxu.com/effects-for-images-lazyload-and-slideshow.html" target="_blank">http://www.brunoxu.com/effects-for-images-lazyload-and-slideshow.html</a></b></p>
	</form>

	<form method="post">
		<div class="clear" style="padding-top:10px;"></div>
		<hr/>
		<h4>If you uploaded effects via ftp, please make sure clicked "Reload Effects" button to use them.</h4>
		<p>
			<input type="submit" class="button" name="op_clear_effects" value=" Reload Effects " onclick="return confirm('Sure to reload effects?')" />
		</p>
	</form>

	<form method="post">
		<div class="clear" style="padding-top:10px;"></div>
		<hr/>
		<h4>Delete effects, be careful!</h4>
		<p>
			<select name="effect_to_delete">
				<?php foreach($available_effects as $effect_name=>$effect): ?>
				<?php if($config["effect"]!=$effect_name): ?>
				<option value="<?php echo htmlspecialchars($effect_name); ?>">
				<?php echo $effect['name']; ?><?php if($config["effect"]==$effect_name) echo '(in use)'; ?>
				</option>
				<?php endif; ?>
				<?php endforeach; ?>
			</select>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<input type="submit" class="button" name="op_delete_effect" value=" Delete Effect " onclick="return confirm('Sure to delete this effect?')" />
		</p>
	</form>
</div>
<?php
	}
}
/** backend end */


/** frontend, apply lazyload and slideshow effect */
if (! is_admin()) {
	$available_effects = lazyload_slideshow_get_available_effects();

	$config = lazyload_slideshow_get_config();

	/** loadyload start */
	$lazyload_applyed = FALSE;
	if ($config["lazyload"]) {
		$lazyload_applyed = TRUE;

		if ($config["lazyload_all"]) {
			add_action('get_header','lazyload_slideshow_lazyload_obstart');//init,get_header,wp_head
			function lazyload_slideshow_lazyload_obstart() {
				ob_start('lazyload_slideshow_lazyload_obend');
			}
			function lazyload_slideshow_lazyload_obend($content) {
				return lazyload_slideshow_lazyload_content_filter($content);
			}
		} else {
			add_filter('the_content', 'lazyload_slideshow_lazyload_content_filter');
		}
		function lazyload_slideshow_lazyload_content_filter($content)
		{
			// Don't lazyload for feeds, previews
			if( is_feed() || is_preview() ) {
				return $content;
			}

			global $config;

			if ($config['lazyload_image_strict_match']) {
				$regexp = "/<img([^<>]*)\.(bmp|gif|jpeg|jpg|png)([^<>]*)>/i";
			} else {
				$regexp = "/<img([^<>]*)>/i";
			}

			$content = preg_replace_callback(
				$regexp,
				"lazyload_slideshow_lazyimg_str_handler",
				$content
			);

			return $content;
		}
		function lazyload_slideshow_lazyimg_str_handler($matches)
		{
			$lazyimg_str = $matches[0];

			// situations do not use lazyload
			if (preg_match("/\/plugins\/wp-postratings\//i", $lazyimg_str)) {
				return $lazyimg_str;
			}

			if (preg_match("/width=/i", $lazyimg_str)
					|| preg_match("/width:/i", $lazyimg_str)
					|| preg_match("/height=/i", $lazyimg_str)
					|| preg_match("/height:/i", $lazyimg_str)) {
				$alt_image_src = Lazyload_Slideshow_Plugin_Url."blank_1x1.gif";
			} else {
				if (preg_match("/\/smilies\//i", $lazyimg_str)
						|| preg_match("/\/smiles\//i", $lazyimg_str)
						|| preg_match("/\/avatar\//i", $lazyimg_str)
						|| preg_match("/\/avatars\//i", $lazyimg_str)) {
					$alt_image_src = Lazyload_Slideshow_Plugin_Url."blank_1x1.gif";
				} else {
					$alt_image_src = Lazyload_Slideshow_Plugin_Url."blank_250x250.gif";
				}
			}

			if (stripos($lazyimg_str, "class=") === FALSE) {
				$lazyimg_str = preg_replace(
					"/<img(.*)>/i",
					'<img class="ls_lazyimg"$1>',
					$lazyimg_str
				);
			} else {
				$lazyimg_str = preg_replace(
					"/<img(.*)class=['\"]([\w\-\s]*)['\"](.*)>/i",
					'<img$1class="$2 ls_lazyimg"$3>',
					$lazyimg_str
				);
			}

			$regexp = "/<img([^<>]*)src=['\"]([^<>'\"]*)['\"]([^<>]*)>/i";
			$replace = '<img$1src="'.$alt_image_src.'" file="$2"$3><noscript>'.$matches[0].'</noscript>';
			$lazyimg_str = preg_replace(
				$regexp,
				$replace,
				$lazyimg_str
			);

			return $lazyimg_str;
		}

		add_action($config["use_footer_or_head"], 'lazyload_slideshow_lazyload_css_and_js');
		function lazyload_slideshow_lazyload_css_and_js()
		{
			print('
<!-- lazyload_slideshow if noscript, hidden lazyload images -->
<noscript>
<style type="text/css">
.ls_lazyimg{display:none;}
</style>
</noscript>
<!-- lazyload_slideshow if noscript, hidden lazyload images end -->

<!-- lazyload_slideshow lazyload images style -->
<style type="text/css">
.ls_lazyimg{
opacity:0.2;filter:alpha(opacity=20);
background:url('.lazyload_slideshow_get_url("loading.gif").') no-repeat center center;
}
</style>
<!-- lazyload_slideshow lazyload images style end -->

<!-- lazyload_slideshow lazyload js -->
<script type="text/javascript">
jQuery(document).ready(function($) {
var ls_lazyload = function() {
	var threshold = 200;
	$("img.ls_lazyimg").each(function(){
		_self = $(this);
		if (_self.attr("lazyloadpass")===undefined
				&& _self.attr("file")
				&& ( !_self.attr("src") || (_self.attr("src") && _self.attr("file")!=_self.attr("src")) )
				) {
			if( (_self.offset().top) < ($(window).height()+$(document).scrollTop()+threshold)
				&& (_self.offset().left) < ($(window).width()+$(document).scrollLeft()+threshold)
				&& (_self.offset().top) > ($(document).scrollTop()-threshold)
				&& (_self.offset().left) > ($(document).scrollLeft()-threshold)
				) {
				_self.attr("src",_self.attr("file"));
				_self.attr("lazyloadpass","1");
				_self.animate({opacity:1},400);
			}
		}
	});
}
ls_lazyload();

var ls_itv;
$(window).scroll(function(){clearTimeout(ls_itv);ls_itv=setTimeout(ls_lazyload,400);});
$(window).resize(function(){clearTimeout(ls_itv);ls_itv=setTimeout(ls_lazyload,400);});
});
</script>
<!-- lazyload_slideshow lazyload js end -->
');
		}
	}
	/** loadyload end */

	/** slideshow start */
	$slideshow_applyed = FALSE;
	if ($config['effect'] && !empty($available_effects[$config['effect']])) {
		$slideshow_applyed = TRUE;

		$effect_use = $available_effects[$config['effect']];
		$adapter_use = $adapter_config = NULL;
		if (! empty($config[$effect_use['name_key'].'-adapter'])) {
			$adapter_config = $config[$effect_use['name_key'].'-adapter'];
		} else {
			$adapter_config = key($effect_use['adapters']);
		}
		$adapter_use = $effect_use['adapters'][$adapter_config];

		if (file_exists($adapter_use['path'])) {
			require_once $adapter_use['path'];
		} else {
			$slideshow_applyed = FALSE;
		}
	}
	/** slideshow end */

	if ($lazyload_applyed || $slideshow_applyed) {
		add_action('wp_enqueue_scripts', 'lazyload_slideshow_script');
		function lazyload_slideshow_script()
		{
			wp_enqueue_script('jquery');
		}
	}

	if ($config["html"]) {
		if (stristr($config["html"], '<div')
				|| stristr($config["html"], '<p')
				|| stristr($config["html"], '<span')
				) {
			add_action('wp_footer', 'lazyload_slideshow_add_html');
		} else {
			add_action($config['use_footer_or_head'], 'lazyload_slideshow_add_html');
		}
		function lazyload_slideshow_add_html()
		{
			global $config;

			print('
<!-- lazyload_slideshow custom html -->
'.stripslashes($config["html"]).'
<!-- lazyload_slideshow custom html end -->
');
		}
	}

}
/** frontend end */
