<?php
/*
Name: Adapter for FancyBox Effect
Author: Bruno Xu 
Author URI: http://blog.brunoxu.info/
Version: one_gallery
Comment: generate one gallery with all content images.
*/

	add_action($lazyload_slideshow_vars["use_footer_or_head"], 'lazyload_slideshow_footer_effect');

	function lazyload_slideshow_footer_effect()
	{
		global $lazyload_slideshow_vars,$is_strict_effect;

		if (! $lazyload_slideshow_vars["add_effect_selector"]) {
			return;
		}

		if ($is_strict_effect) {
			$regexp = '/.+(\.jpg)|(\.jpeg)|(\.png)|(\.gif)|(\.bmp)/i';
		} else {
			$regexp = '/.+/';
		}

		print('
<!-- '.$lazyload_slideshow_vars["effect"].' -->
<script type="text/javascript" src="'.get_bloginfo('wpurl').'/wp-content/plugins/images-lazyload-and-slideshow/'.$lazyload_slideshow_vars["effect"].'/fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
<script type="text/javascript" src="'.get_bloginfo('wpurl').'/wp-content/plugins/images-lazyload-and-slideshow/'.$lazyload_slideshow_vars["effect"].'/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<link rel="stylesheet" type="text/css" href="'.get_bloginfo('wpurl').'/wp-content/plugins/images-lazyload-and-slideshow/'.$lazyload_slideshow_vars["effect"].'/fancybox/jquery.fancybox-1.3.4.css" media="screen" />
<script type="text/javascript">
jQuery(function($){
	$("'.$lazyload_slideshow_vars["add_effect_selector"].'").each(function(i){
		_self = $(this);

		selfWidth = _self.attr("width")?_self.attr("width"):_self.width();
		selfHeight = _self.attr("height")?_self.attr("height"):_self.height();
		if ((selfWidth && selfWidth<50)
				|| (selfHeight && selfHeight<50)) {
			return;
		}

		if (! this.parentNode.href) {
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
				_self.addClass("slideshow_imgs");
				_self.wrap("<a href=\'"+imgsrc+"\' rel=\'fancygallery\'></a>");
			}
		} else {
			aHref = this.parentNode.href;
			var b='.$regexp.';
			if (! b.test(aHref)) {
				return;
			}

			_self.addClass("slideshow_imgs");

			_parentA = $(this.parentNode);
			rel = _parentA.attr("rel");
			if (! rel) {
				rel = "";
			}
			if (rel.indexOf("fancygallery") != 0) {
				_parentA.attr("rel","fancygallery");
			}
		}
	});

	$("a[rel=fancygallery]").fancybox({
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
<!-- '.$lazyload_slideshow_vars["effect"].' end -->
');
	}

?>