<?php
/*
Plugin Name: Images Lazyload and Slideshow
Plugin URI: http://blog.brunoxu.info/images-lazyload-and-slideshow/
Description: This plugin is highly intelligent and useful, it contains four gadgets: Customized css for content images, Images True Lazyload realization, Slideshow Effects using FancyBox or prettyPhoto etc, Tracking Code Setting.
Author: Bruno Xu
Author URI: http://blog.brunoxu.info/
Version: 2.4
*/

define('ImagesLS_Name', 'Images Lazyload and Slideshow');
define('ImagesLS_Version', '2.4');
define('ImagesLS_Config_Name', "lazyload_slideshow_config");

$adapter_key = "apply_effect";
$adapter_connector = "-";
$support_effects = array(
	"fancybox"=>array(
		"adapters"=>array("two_galleries","one_gallery","single_image")
	),
	"prettyPhoto"=>array(
		"adapters"=>array("two_galleries","one_gallery","single_image")
	),
	"slimbox2"=>array(
		"adapters"=>array("one_gallery","single_image")
	),
);

$limit_width_selector = $add_effect_selector = "#content img,.content img,.archive img,.post img,.page img";

$is_strict_lazyload = FALSE;
$is_strict_effect = TRUE;

$css_reference = '
<style type="text/css">
/* maxwidth limit for content images */
'.$limit_width_selector.'{
margin-top:3px;max-width:600px;
height:auto !important;
_width:expression(this.width>600?600:auto);
}
/* style for slideshow images */
.slideshow_imgs{cursor:url(http://simplenotestheme.googlecode.com/svn/trunk/autohighslide/highslide/graphics/zoomin.cur), pointer;}
.slideshow_imgs:hover{opacity:0.5 !important;filter:alpha(opacity=50) !important;}
#fancybox-wrap{cursor:url(http://simplenotestheme.googlecode.com/svn/trunk/autohighslide/highslide/graphics/zoomout.cur), pointer;}
</style>
';

$tracking_code_reference = '
<!-- baidu tongji -->
<div style="width:0;height:0;overflow:hidden;">
<script type="text/javascript">
var _bdhmProtocol = (("https:" == document.location.protocol) ? " https://" : " http://");
document.write(unescape("%3Cscript src=\'" + _bdhmProtocol + "hm.baidu.com/h.js%3Fed87e845538b0fe86a4caf1d0018e458\' type=\'text/javascript\'%3E%3C/script%3E"));
</script>
</div>
';

function lazyload_slideshow_get_url($path='')
{
	return plugins_url(ltrim($path, '/'), __FILE__);
}



$lazyload_slideshow_vars = get_option(ImagesLS_Config_Name);
if (! $lazyload_slideshow_vars) {
	$lazyload_slideshow_vars = array(
		"css" => "",
		"lazyload" => "1",
		"effect" => key($support_effects),
		"add_effect_selector" => $add_effect_selector,
		"tracking_code" => "",
		"use_footer_or_head" => "wp_footer",
	);
	add_option(ImagesLS_Config_Name, $lazyload_slideshow_vars);
} else {
	$need_updating = false;

	if (! isset($lazyload_slideshow_vars["add_effect_selector"])) {
		$lazyload_slideshow_vars["add_effect_selector"] = $add_effect_selector;
		$need_updating = true;
	}
	if (! isset($lazyload_slideshow_vars["use_footer_or_head"])) {
		$lazyload_slideshow_vars["use_footer_or_head"] = "wp_footer";
		$need_updating = true;
	}

	if ($need_updating) {
		update_option(ImagesLS_Config_Name, $lazyload_slideshow_vars);
	}
}

if (! is_admin()) {
	if ($lazyload_slideshow_vars["css"]) {
		add_action($lazyload_slideshow_vars["use_footer_or_head"], 'lazyload_slideshow_footer_css');
	}

	if ($lazyload_slideshow_vars["lazyload"]) {
		lazyload_slideshow_lazyload();
	}

	if ($lazyload_slideshow_vars["effect"]
			&& isset($support_effects[$lazyload_slideshow_vars["effect"]])) {
		$adapter = "";
		if (isset($lazyload_slideshow_vars[$lazyload_slideshow_vars["effect"]."-adapter"])
				&& $lazyload_slideshow_vars[$lazyload_slideshow_vars["effect"]."-adapter"]) {
			$adapter = $lazyload_slideshow_vars[$lazyload_slideshow_vars["effect"]."-adapter"];
		} else {
			$adapter = $support_effects[$lazyload_slideshow_vars["effect"]]["adapters"][0];
		}
		require_once $lazyload_slideshow_vars["effect"]."/$adapter_key".($adapter?($adapter_connector.$adapter):"").".php";
	}

	if ($lazyload_slideshow_vars["lazyload"]
			|| ($lazyload_slideshow_vars["effect"]
					&& isset($support_effects[$lazyload_slideshow_vars["effect"]]))) {
		add_action('wp_enqueue_scripts', 'lazyload_slideshow_script');
	}

	if ($lazyload_slideshow_vars["tracking_code"]) {
		add_action($lazyload_slideshow_vars["use_footer_or_head"], 'lazyload_slideshow_footer_tracking_code');
	}
} else {
	add_action('admin_menu','lazyload_slideshow_admin_menu');

	add_filter('plugin_action_links', 'add_lazyload_slideshow_settings_link', 10, 2);
	function add_lazyload_slideshow_settings_link($links, $file) {
		static $this_plugin;
		if (!$this_plugin) $this_plugin = plugin_basename(__FILE__);

		if ($file == $this_plugin){
			$settings_link = '<a href="'.wp_nonce_url("options-general.php?page=images-lazyload-and-slideshow/lazyload-slideshow.php").'">Setting</a>';
			array_unshift($links, $settings_link);
		}
		return $links;
	}
}

function lazyload_slideshow_footer_css()
{
	global $lazyload_slideshow_vars;

	if ($lazyload_slideshow_vars["css"]) {
		print('
<!-- lazyload_slideshow css for images in content -->
'.stripslashes($lazyload_slideshow_vars["css"]).'
<!-- lazyload_slideshow css for images in content end -->
');
	}
}

function lazyload_slideshow_lazyload()
{
	global $lazyload_slideshow_vars;

	//init,get_header,wp_head
	add_action('get_header','lazyload_slideshow_obstart');
	function lazyload_slideshow_obstart() {
		ob_start();
	}

	//get_footer,wp_footer,shutdown(NG)
	add_action('wp_footer','lazyload_slideshow_obend');
	function lazyload_slideshow_obend() {
		$echo = ob_get_contents(); //获取缓冲区内容
		ob_clean(); //清楚缓冲区内容，不输出到页面
		print lazyload_slideshow_content_filter_lazyload($echo); //重新写入的缓冲区
		ob_end_flush(); //将缓冲区输入到页面，并关闭缓存区
	}

	function lazyimg_str_handler($matches)
	{
		global $is_strict_lazyload;

		$lazyimg_str = $matches[0];

		//不需要lazyload的情况
		if (preg_match("/\/plugins\/wp-postratings\//i", $lazyimg_str)) {
			return $lazyimg_str;
		}

		if (preg_match("/width=/i", $lazyimg_str)
				|| preg_match("/width:/i", $lazyimg_str)
				|| preg_match("/height=/i", $lazyimg_str)
				|| preg_match("/height:/i", $lazyimg_str)) {
			$alt_image_src = lazyload_slideshow_get_url("blank_1x1.gif");
		} else {
			if (preg_match("/\/smilies\//i", $lazyimg_str)
					|| preg_match("/\/smiles\//i", $lazyimg_str)
					|| preg_match("/\/avatar\//i", $lazyimg_str)
					|| preg_match("/\/avatars\//i", $lazyimg_str)) {
				$alt_image_src = lazyload_slideshow_get_url("blank_1x1.gif");
			} else {
				$alt_image_src = lazyload_slideshow_get_url("blank_250x250.gif");
			}
		}

		if (stripos($lazyimg_str, "class=") === FALSE) {
			$lazyimg_str = preg_replace(
				"/<img(.*)>/i",
				'<img class="lh_lazyimg"$1>',
				$lazyimg_str
			);
		} else {
			$lazyimg_str = preg_replace(
				"/<img(.*)class=['\"]([\w\-\s]*)['\"](.*)>/i",
				'<img$1class="$2 lh_lazyimg"$3>',
				$lazyimg_str
			);
		}

		if ($is_strict_lazyload) {
			$regexp = "/<img([^<>]*)src=['\"]([^<>'\"]*)\.(bmp|gif|jpeg|jpg|png)([^<>'\"]*)['\"]([^<>]*)>/i";
			$replace = '<img$1src="'.$alt_image_src.'" file="$2.$3$4"$5><noscript>'.$matches[0].'</noscript>';
		} else {
			$regexp = "/<img([^<>]*)src=['\"]([^<>'\"]*)['\"]([^<>]*)>/i";
			$replace = '<img$1src="'.$alt_image_src.'" file="$2"$3><noscript>'.$matches[0].'</noscript>';
		}

		$lazyimg_str = preg_replace(
			$regexp,
			$replace,
			$lazyimg_str
		);

		return $lazyimg_str;
	}

	function lazyload_slideshow_content_filter_lazyload($content)
	{
		// Don't lazyload for feeds, previews, mobile
		if( is_feed() || is_preview() || ( function_exists( 'is_mobile' ) && is_mobile() ) )
			return $content;

		global $is_strict_lazyload;

		if ($is_strict_lazyload) {
			$regexp = "/<img([^<>]*)\.(bmp|gif|jpeg|jpg|png)([^<>]*)>/i";
		} else {
			$regexp = "/<img([^<>]*)>/i";
		}

		$content = preg_replace_callback(
			$regexp,
			"lazyimg_str_handler",
			$content
		);

		return $content;
	}


	add_action($lazyload_slideshow_vars["use_footer_or_head"], 'lazyload_slideshow_footer_lazyload');
	function lazyload_slideshow_footer_lazyload()
	{
		print('
<!-- lazyload images -->
<style type="text/css">
.lh_lazyimg{
opacity:0.2;filter:alpha(opacity=20);
background:url('.lazyload_slideshow_get_url("loading.gif").') no-repeat center center;
}
</style>
<!-- lazyload images end -->

<!-- case nojs, hidden lazyload images -->
<noscript>
<style type="text/css">
.lh_lazyimg{display:none;}
</style>
</noscript>
<!-- case nojs, hidden lazyload images end -->

<!-- lazyload -->
<script type="text/javascript">
jQuery(document).ready(function($) {
	function lazyload(){
		$("img.lh_lazyimg").each(function(){
			_self = $(this);
			if (_self.attr("lazyloadpass")===undefined
					&& _self.attr("file")
					&& (!_self.attr("src")
							|| (_self.attr("src") && _self.attr("file")!=_self.attr("src"))
						)
				) {
				if((_self.offset().top) < ($(window).height()+$(document).scrollTop()+200)
						&& (_self.offset().left) < ($(window).width()+$(document).scrollLeft()+200)
					) {
					_self.attr("src",_self.attr("file"));
					_self.attr("lazyloadpass", "1");
					_self.animate({opacity:1}, 400);
				}
			}
		});
	}
	lazyload();

	var itv;
	$(window).scroll(function(){clearTimeout(itv);itv=setTimeout(lazyload,400);});
	$(window).resize(function(){clearTimeout(itv);itv=setTimeout(lazyload,400);});
});
</script>
<!-- lazyload end -->
');
	}
}

function lazyload_slideshow_script()
{
	wp_enqueue_script('jquery');
}

function lazyload_slideshow_footer_tracking_code()
{
	global $lazyload_slideshow_vars;

	if ($lazyload_slideshow_vars["tracking_code"]) {
		print('
<!-- lazyload_slideshow tracking code -->
'.stripslashes($lazyload_slideshow_vars["tracking_code"]).'
<!-- lazyload_slideshow tracking code end -->
');
	}
}


// add admin menu
function lazyload_slideshow_admin_menu()
{
	add_options_page(
		ImagesLS_Name.' Setting',
		ImagesLS_Name,
		8,
		__FILE__,
		'lazyload_slideshow_config_page'
	);
}

// lazyload_slideshow config page
function lazyload_slideshow_config_page()
{
	global $add_effect_selector,
			$css_reference,$tracking_code_reference,
			$lazyload_slideshow_vars,$support_effects;

	if ($_POST && isset($_POST['op_save'])) {
		$css_post = "";
		$lazyload_post = "";
		$effect_post = "";
		$add_effect_selector_post = "";
		$tracking_code_post = "";
		$use_footer_or_head_post = "";

		if (isset($_POST['css']) && trim($_POST['css'])) {
			$css_post = trim($_POST['css']);
		}
		if (isset($_POST['lazyload']) && $_POST['lazyload']) {
			$lazyload_post = $_POST['lazyload'];
		}
		if (isset($_POST['effect']) && $_POST['effect']) {
			$effect_post = $_POST['effect'];
		}
		if (isset($_POST['add_effect_selector']) && trim($_POST['add_effect_selector'])) {
			$add_effect_selector_post = trim($_POST['add_effect_selector']);
		}
		if (isset($_POST['tracking_code']) && trim($_POST['tracking_code'])) {
			$tracking_code_post = trim($_POST['tracking_code']);
		}
		if (isset($_POST['use_footer_or_head']) && $_POST['use_footer_or_head']) {
			$use_footer_or_head_post = $_POST['use_footer_or_head'];
		}

		$lazyload_slideshow_vars["css"] = $css_post;
		$lazyload_slideshow_vars["lazyload"] = $lazyload_post;
		$lazyload_slideshow_vars["effect"] = $effect_post;
		if ($effect_post) {
			$lazyload_slideshow_vars["$effect_post-adapter"] = $_POST["$effect_post-adapter"];
		}
		$lazyload_slideshow_vars["add_effect_selector"] = $add_effect_selector_post;
		$lazyload_slideshow_vars["tracking_code"] = $tracking_code_post;
		$lazyload_slideshow_vars["use_footer_or_head"] = $use_footer_or_head_post;

		update_option(ImagesLS_Config_Name, $lazyload_slideshow_vars);

		$lazyload_slideshow_vars = get_option(ImagesLS_Config_Name);

		echo '<div class="updated"><strong><p>Saved Successfully</p></strong></div>';
	}
?>
	<div class="wrap">
<style type="text/css">
.wrap{min-width:1080px;_width:1080px;}

dt,dd{padding:15px 3px 0;}
dt{float:left;width:150px;clear:both;}
dd{float:left;*float:none;*display:inline-block;margin:0;}

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
</style>

		<h2><?php echo ImagesLS_Name.' Setting'; ?></h2>

		<form method="post">
			<div style="clear: both;padding-top:10px;"></div>
			<p><strong>Plugin Name :</strong> <?php echo ImagesLS_Name; ?>
			</p>
			<p><strong>Plugin Version :</strong> <?php echo ImagesLS_Version; ?>
			</p>
			<p><strong>Plugin Author :</strong> <a href="http://blog.brunoxu.info/" target="_blank">Bruno Xu</a> (xuguangzhi2003@126.com)
			</p>
			<p><strong>Q & A :</strong> <a href="http://blog.brunoxu.info/images-lazyload-and-slideshow/" target="_blank">http://blog.brunoxu.info/images-lazyload-and-slideshow/</a>
			</p>

			<div style="clear: both;padding-top:10px;"></div>
			<hr/>

			<dl>
				<dt><strong>CSS For Images :</strong></dt>
				<dd>
					<textarea name="css" style="width:440px;height:271px;"><?php echo stripslashes($lazyload_slideshow_vars["css"]); ?></textarea>
				</dd>
				<div style="width:440px;height:271px;float:left;padding:15px 0 0 5px;overflow:hidden;">
					<div class="tips"><pre><?php echo "<b>Sample:</b>".htmlentities($css_reference); ?></pre></div>
				</div>
			</dl>

			<dl>
				<dt><strong>Use Lazyload :</strong></dt>
				<dd>
					<input type="checkbox" name="lazyload" value="1" <?php if($lazyload_slideshow_vars["lazyload"]) echo 'checked="true"'; ?> />
				</dd>
			</dl>

			<dl>
				<dt><strong>Use Slideshow :</strong></dt>
				<dd>
					<select name="effect" onchange="onChangeEffect(this)">
						<option value="">Do Not Use</option>
						<?php foreach($support_effects as $eff=>$conf): ?>
							<option value="<?php echo $eff; ?>" <?php if($lazyload_slideshow_vars["effect"]==$eff) echo ' selected="true"'; ?>><?php echo $eff; ?></option>
						<?php endforeach; ?>
					</select>
					<span id="spanAdapterSelect" style="padding: 0 0 0 50px;">
						Adapters :
						<?php foreach($support_effects as $effect=>$configs): ?>
						<select id="<?php echo "select-$effect-adapter"; ?>"
							name="<?php echo "$effect-adapter"; ?>"
							<?php if($lazyload_slideshow_vars["effect"]!=$effect) echo ' style="display:none;"'; ?>
						>
							<?php foreach($configs["adapters"] as $adapkey=>$adap): ?>
							<option value="<?php echo $adap; ?>"
								<?php
									if(isset($lazyload_slideshow_vars["$effect-adapter"])
											&& $lazyload_slideshow_vars["$effect-adapter"]
											&& $lazyload_slideshow_vars["$effect-adapter"]==$adap
											)
										echo ' selected="true"';
								?>
							>
								<?php echo $adap; ?>
							</option>
							<?php endforeach; ?>
						</select>
						<?php endforeach; ?>
					</span>
				</dd>
				<script type="text/javascript">
				function onChangeEffect(domEffect){
					adapters = document.getElementById("spanAdapterSelect").children;
					for (var i=0;i<adapters.length;i++){
						adapters[i].style.display = "none";
					}

					selected_effect = domEffect.value;
					if (selected_effect) {
						document.getElementById("spanAdapterSelect").style.display = "";
						adapter_show = document.getElementById("select-"+selected_effect+"-adapter");
						adapter_show.style.display = "";
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

			<dl id="dlAddEffectSelector"<?php if(! $lazyload_slideshow_vars["effect"]) echo ' style="display:none;"'; ?>>
				<dt><strong>Add Effect Selector :</strong></dt>
				<dd>
					<input type="text" name="add_effect_selector"
						value="<?php echo htmlentities($lazyload_slideshow_vars["add_effect_selector"]); ?>"
						style="width:800px;"/>
					<div class="tips">
						<pre><?php echo "<b>Sample:</b>\n".$add_effect_selector; ?></pre>
					</div>
				</dd>
			</dl>

			<dl>
				<dt><strong>Tracking Code :</strong></dt>
				<dd>
					<textarea name="tracking_code" style="width:440px;height:280px;"><?php echo stripslashes($lazyload_slideshow_vars["tracking_code"]); ?></textarea>
				</dd>
				<div style="width:440px;height:280px;float:left;padding:15px 0 0 5px;overflow:hidden;">
					<div class="tips"><pre><?php echo "<b>Sample:</b>".htmlentities($tracking_code_reference); ?></pre></div>
				</div>
			</dl>

			<dl>
				<dt><strong>Action to Hook :</strong></dt>
				<dd>
					<select name="use_footer_or_head">
						<option value="wp_footer"<?php if ($lazyload_slideshow_vars["use_footer_or_head"]=="wp_footer") echo ' selected="true"'; ?>>wp_footer</option>
						<option value="wp_head"<?php if ($lazyload_slideshow_vars["use_footer_or_head"]=="wp_head") echo ' selected="true"'; ?>>wp_head</option>
					</select>
				</dd>
			</dl>

			<div style="clear: both;padding-top:10px;"></div>
			<hr/>

			<p class="submit" style="clear:both;"><input type="submit" name="op_save" value=" SAVE " /></p>
		</form>
	</div>
<?php
}
?>