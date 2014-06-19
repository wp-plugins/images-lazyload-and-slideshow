<?php
/*
Adapter Name: two_galleries
Adapter URI: 
Description: generate two galleries, images has href and the other has not
Author: Bruno Xu 
Author URI: http://www.brunoxu.com/
Version: 1.0
*/

$force_not_strict_match = FALSE;
$no_href_img_included = TRUE;

add_action($config['use_footer_or_head'], 'lazyload_slideshow_add_effect');
function lazyload_slideshow_add_effect()
{
	global $config, $effect_use, $adapter_use;
	global $no_href_img_included,$force_not_strict_match;

	$item_width_height_check = TRUE;

	if (! $config["add_effect_selector"]) {
		return;
	}

	if ($force_not_strict_match) {
		$regexp = '/.+/';
		$item_width_height_check = FALSE;
	} elseif ($config['effect_image_strict_match']) {
		$regexp = '/.+(\.jpg)|(\.jpeg)|(\.png)|(\.gif)|(\.bmp)/i';
	} else {
		$regexp = '/.+/';
		$item_width_height_check = FALSE;
	}

	print('
<!-- '.$effect_use["name"].' / '.$adapter_use["name"].' '.$adapter_use['version'].' -->
<link rel="stylesheet" href="'.Lazyload_Slideshow_Effect_Url.$effect_use["folder_name"].'/fancybox/jquery.fancybox-1.3.4.css" type="text/css" media="screen"/>
<script type="text/javascript" src="'.Lazyload_Slideshow_Effect_Url.$effect_use["folder_name"].'/fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
<script type="text/javascript" src="'.Lazyload_Slideshow_Effect_Url.$effect_use["folder_name"].'/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<script type="text/javascript">
jQuery(function($){
	$("'.$config["add_effect_selector"].'").each(function(i){
		_self = $(this);

		'.($item_width_height_check?'selfWidth = _self.attr("width")?_self.attr("width"):_self.width();
		selfHeight = _self.attr("height")?_self.attr("height"):_self.height();
		if ((selfWidth && selfWidth<50)
				|| (selfHeight && selfHeight<50)) {
			return;
		}':'').'

		if (this.parentNode.href) {
			aHref = this.parentNode.href;
			var b='.$regexp.';
			if (! b.test(aHref)) {
				return;
			}

			_self.addClass("ls_slideshow_imgs");

			_parentA = $(this.parentNode);
			rel = _parentA.attr("rel");
			if (! rel) {
				rel = "";
			}
			if (rel.indexOf("fancygallery") != 0) {
				_parentA.attr("rel","fancygallery1");
			}
		}'.($no_href_img_included?' else {
			imgsrc = "";
			if (_self.attr("src")) {
				imgsrc = _self.attr("src");
			}
			if (_self.attr("file")) {
				imgsrc = _self.attr("file");
			} else if (_self.attr("original")) {
				imgsrc = _self.attr("original");
			}

			if (imgsrc) {
				_self.addClass("ls_slideshow_imgs");
				_self.wrap("<a href=\'"+imgsrc+"\' rel=\'fancygallery2\'></a>");
			}
		}':'').'
	});

	$("a[rel^=fancygallery]").fancybox({
		\'overlayShow\'		: true,
		\'hideOnContentClick\'		: false,
		\'transitionIn\'		: \'elastic\',
		\'transitionOut\'		: \'elastic\',
		\'titlePosition\' 	: \'over\',
		\'titleFormat\'		: function(title, currentArray, currentIndex, currentOpts) {
			return \'<span id="fancybox-title-over">Image \' + (currentIndex + 1) + \' / \' + currentArray.length + (title.length ? \' &nbsp; \' + title : \'\') + \'</span>\';
		}
	});
});
</script>
<!-- '.$effect_use["name"].' / '.$adapter_use["name"].' '.$adapter_use['version'].' end -->
');
}
