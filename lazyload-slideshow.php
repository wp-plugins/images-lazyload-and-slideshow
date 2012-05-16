<?php
/*
Plugin Name: Images Lazyload and Slideshow
Plugin URI: http://blog.brunoxu.info/images-lazyload-and-slideshow/
Description: This plugin is highly intelligent and useful, it contains four gadgets: Customized css for content images, Image True Lazyload realization, Slideshow Effect using FancyBox and prettyPhoto, Tracking Code Setting.
Author: Bruno Xu 
Author URI: http://blog.brunoxu.info/
Version: 1.2
*/

define('ImagesLS_Name', 'Images Lazyload and Slideshow');
define('ImagesLS_Version', '1.2');
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
);

$css_reference = '
<style type="text/css">
/* maxwidth limit for content images */
#content img,.content img,.archive img,.post img {
margin-top:3px;max-width:600px;
_width:expression(this.width>600?600:auto);
}
/* style for slideshow images */
.slideshow_imgs {
cursor:url(http://simplenotestheme.googlecode.com/svn/trunk/autohighslide/highslide/graphics/zoomin.cur), pointer;
}
.slideshow_imgs:hover{opacity:0.5;filter:alpha(opacity=50);}
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

$lazyload_slideshow_vars = get_option(ImagesLS_Config_Name);
if (! $lazyload_slideshow_vars) {
	$lazyload_slideshow_vars = array(
		"css" => "",
		"lazyload" => "1",
		"effect" => key($support_effects),
		"tracking_code" => ""
	);
	add_option(ImagesLS_Config_Name, $lazyload_slideshow_vars);
}

if (! is_admin()) {
	if ($lazyload_slideshow_vars["css"]) {
		add_action('wp_footer', 'lazyload_slideshow_footer_css');
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
		add_action('wp_footer', 'lazyload_slideshow_footer_tracking_code');
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
	add_filter('the_content', 'lazyload_slideshow_content_filter_lazyload');

	function lazyimg_str_handler($matches) {
		//$alt_image_src = get_bloginfo('wpurl') . '/wp-content/plugins/images-lazyload-and-slideshow/blank_image.gif';
		$alt_image_src = get_bloginfo('wpurl') . '/wp-content/plugins/images-lazyload-and-slideshow/loading_2.gif';

		$lazyimg_str = $matches[0];

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

		$lazyimg_str = preg_replace(
			"/<img([^<>]*)src=['\"]([^<>]*)\.(bmp|gif|jpeg|jpg|png)['\"]([^<>]*)>/i",
			'<img$1src="'.$alt_image_src.'" file="$2.$3"$4><noscript>'.$matches[0].'</noscript>',
			$lazyimg_str
		);

		return $lazyimg_str;
	}

	function lazyload_slideshow_content_filter_lazyload($content)
	{
		$content = preg_replace_callback(
			"/<img([^<>]*)>/i",
			"lazyimg_str_handler",
			$content
		);

		return $content;
	}


	add_action('wp_footer', 'lazyload_slideshow_footer_lazyload');

	function lazyload_slideshow_footer_lazyload()
	{
		print('
<!-- hidden lazyload image -->
<noscript>
<style type="text/css">
.lh_lazyimg{display:none;}
</style>
</noscript>
<!-- hidden lazyload image end -->

<!-- lazyload -->
<script type="text/javascript">
jQuery(document).ready(function($) {
	function lazyload(){
		$("img").each(function(){
			_self = $(this);
			if (_self.attr("file")
					&& (!_self.attr("src")
						|| (_self.attr("src") && _self.attr("file")!=_self.attr("src"))
						)
				) {
				if((_self.offset().top) < $(window).height()+$(document).scrollTop()
						&& (_self.offset().left) < $(window).width()+$(document).scrollLeft()
					) {
					_self.attr("src",_self.attr("file"));
				}
			}
		});
	}
	lazyload();
	$(window).scroll(lazyload);
	$(window).resize(lazyload);
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
	global $css_reference,$tracking_code_reference,
			$lazyload_slideshow_vars,$support_effects;

	if ($_POST && isset($_POST['op_save'])) {
		$css_post = "";
		$lazyload_post = "";
		$effect_post = "";
		$tracking_code_post = "";

		if (isset($_POST['css']) && trim($_POST['css'])) {
			$css_post = trim($_POST['css']);
		}
		if (isset($_POST['lazyload']) && $_POST['lazyload']) {
			$lazyload_post = $_POST['lazyload'];
		}
		if (isset($_POST['effect']) && $_POST['effect']) {
			$effect_post = $_POST['effect'];
		}
		if (isset($_POST['tracking_code']) && trim($_POST['tracking_code'])) {
			$tracking_code_post = $_POST['tracking_code'];
		}
		
		$lazyload_slideshow_vars["css"] = $css_post;
		$lazyload_slideshow_vars["lazyload"] = $lazyload_post;
		$lazyload_slideshow_vars["effect"] = $effect_post;
		$lazyload_slideshow_vars["tracking_code"] = $tracking_code_post;
		if ($effect_post) {
			$lazyload_slideshow_vars["$effect_post-adapter"] = $_POST["$effect_post-adapter"];
		}

		update_option(ImagesLS_Config_Name, $lazyload_slideshow_vars);

		$lazyload_slideshow_vars = get_option(ImagesLS_Config_Name);

		echo '<div class="updated"><strong><p>Saved Successfully</p></strong></div>';
	}
?>
	<div class="wrap">
<style type="text/css">
dt,dd{padding:5px 3px 0;}
dt{float:left;width:135px;clear:both;}
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
				<div style="width:440px;height:271px;float:left;padding:5px 0 0 5px;overflow:hidden;">
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
				}
				</script>
			</dl>

			<dl>
				<dt><strong>Tracking Code :</strong></dt>
				<dd>
					<textarea name="tracking_code" style="width:440px;height:280px;"><?php echo stripslashes($lazyload_slideshow_vars["tracking_code"]); ?></textarea>
				</dd>
				<div style="width:440px;height:280px;float:left;padding:5px 0 0 5px;overflow:hidden;">
					<div class="tips"><pre><?php echo "<b>Sample:</b>".htmlentities($tracking_code_reference); ?></pre></div>
				</div>
			</dl>

			<div style="clear: both;padding-top:10px;"></div>
			<hr/>

			<p class="submit" style="clear:both;"><input type="submit" name="op_save" value=" SAVE " /></p>
		</form>
	</div>
<?php
}
?>