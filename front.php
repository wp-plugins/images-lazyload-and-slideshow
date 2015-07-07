<?php if (!defined('ABSPATH')) exit;

$available_effects = lazyload_slideshow_get_available_effects();
$config = lazyload_slideshow_get_config();

/** lazy loading start */
$lazyload_applyed = FALSE;
if ($config["lazyload"]) {
	$lazyload_applyed = TRUE;

	if ($config["lazyload_all"]) {
		add_action('template_redirect','lazyload_slideshow_lazyload_obstart');
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
		$skip_lazyload = apply_filters('lazyload_slideshow_skip_lazyload', false);

		// don't lazyload for feeds, previews
		if( $skip_lazyload || is_feed() || is_preview() ) {
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

		// no need to use lazy load
		if (stripos($lazyimg_str, 'src=') === FALSE) {
			return $lazyimg_str;
		}
		if (stripos($lazyimg_str, 'skip_lazyload') !== FALSE) {
			return $lazyimg_str;
		}
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
<!-- '.Lazyload_Slideshow_Name.' '.Lazyload_Slideshow_Version.' - lazyload css and js -->
<style type="text/css">
.ls_lazyimg{
opacity:0.1;filter:alpha(opacity=10);
background:url('.Lazyload_Slideshow_Plugin_Url.'loading.gif'.') no-repeat center center;
}
</style>

<noscript>
<style type="text/css">
.ls_lazyimg{display:none;}
</style>
</noscript>

<script type="text/javascript">
Array.prototype.S = String.fromCharCode(2);
Array.prototype.in_array = function(e) {
	var r = new RegExp(this.S+e+this.S);
	return (r.test(this.S+this.join(this.S)+this.S));
};

Array.prototype.pull=function(content){
	for(var i=0,n=0;i<this.length;i++){
		if(this[i]!=content){
			this[n++]=this[i];
		}
	}
	this.length-=1;
};

jQuery(function($) {
window._lazyimgs = $("img.ls_lazyimg");
if (_lazyimgs.length == 0) {
	return;
}
var toload_inds = [];
var loaded_inds = [];
var failed_inds = [];
var failed_count = {};
var lazyload = function() {
	if (loaded_inds.length==_lazyimgs.length) {
		return;
	}
	var threshold = 200;
	_lazyimgs.each(function(i){
		_self = $(this);
		if ( _self.attr("lazyloadpass")===undefined && _self.attr("file")
			&& ( !_self.attr("src") || (_self.attr("src") && _self.attr("file")!=_self.attr("src")) )
			) {
			if( (_self.offset().top) < ($(window).height()+$(document).scrollTop()+threshold)
				&& (_self.offset().left) < ($(window).width()+$(document).scrollLeft()+threshold)
				&& (_self.offset().top) > ($(document).scrollTop()-threshold)
				&& (_self.offset().left) > ($(document).scrollLeft()-threshold)
				) {
				if (toload_inds.in_array(i)) {
					return;
				}
				toload_inds.push(i);
				if (failed_count["count"+i] === undefined) {
					failed_count["count"+i] = 0;
				}
				_self.css("opacity",1);
				$("<img ind=\""+i+"\"/>").bind("load", function(){
					var ind = $(this).attr("ind");
					if (loaded_inds.in_array(ind)) {
						return;
					}
					loaded_inds.push(ind);
					var _img = _lazyimgs.eq(ind);
					_img.attr("src",_img.attr("file")).css("background-image","none").attr("lazyloadpass","1");
				}).bind("error", function(){
					var ind = $(this).attr("ind");
					if (!failed_inds.in_array(ind)) {
						failed_inds.push(ind);
					}
					failed_count["count"+ind]++;
					if (failed_count["count"+ind] < 2) {
						toload_inds.pull(ind);
					}
				}).attr("src", _self.attr("file"));
			}
		}
	});
}
lazyload();
var ins;
$(window).scroll(function(){clearTimeout(ins);ins=setTimeout(lazyload,100);});
$(window).resize(function(){clearTimeout(ins);ins=setTimeout(lazyload,100);});
});

jQuery(function($) {
var calc_image_height = function(_img) {
	var width = _img.attr("width");
	var height = _img.attr("height");
	if ( !(width && height && width>=300) ) return;
	var now_width = _img.width();
	var now_height = parseInt(height * (now_width/width));
	_img.css("height", now_height);
}
var fix_images_height = function() {
	_lazyimgs.each(function() {
		calc_image_height($(this));
	});
}
fix_images_height();
$(window).resize(fix_images_height);
});
</script>
<!-- '.Lazyload_Slideshow_Name.' '.Lazyload_Slideshow_Version.' - lazyload css and js END -->
');
	}
}
/** lazy loading END */

/** slideshow */
$slideshow_applyed = FALSE;
if ($config['effect'] && !empty($available_effects[$config['effect']]) && !wp_is_mobile()) {
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
/** slideshow END */

/** enqueue js */
if ($lazyload_applyed || $slideshow_applyed) {
	add_action('wp_enqueue_scripts', 'lazyload_slideshow_script');
	function lazyload_slideshow_script()
	{
		wp_enqueue_script('jquery');
	}
}
/** enqueue js END */

/** custom html */
if ($config["html"]) {
	if (stristr($config["html"], '<div')!==FALSE
			|| stristr($config["html"], '<p')!==FALSE
			|| stristr($config["html"], '<span')!==FALSE
			) {
		add_action('wp_footer', 'lazyload_slideshow_add_html');
	} else {
		add_action($config['use_footer_or_head'], 'lazyload_slideshow_add_html');
	}
	function lazyload_slideshow_add_html()
	{
		global $config;

		print('
<!-- '.Lazyload_Slideshow_Name.' '.Lazyload_Slideshow_Version.' custom html -->
'.stripslashes($config["html"]).'
<!-- '.Lazyload_Slideshow_Name.' '.Lazyload_Slideshow_Version.' custom html END -->
');
	}
}
/** custom html END */
