<?php
/*
Name: Adapter for slimbox2 Effect
Author: Bruno Xu 
Author URI: http://blog.brunoxu.info/
Version: single_image
Comment: single effect.
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
<link rel="stylesheet" href="'.get_bloginfo('wpurl').'/wp-content/plugins/images-lazyload-and-slideshow/'.$lazyload_slideshow_vars["effect"].'/css/slimbox2.css" type="text/css" media="screen"/>
<script type="text/javascript" src="'.get_bloginfo('wpurl').'/wp-content/plugins/images-lazyload-and-slideshow/'.$lazyload_slideshow_vars["effect"].'/js/slimbox2.js"></script>
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
				_self.wrap("<a href=\'"+imgsrc+"\' rel=\'lightbox\'></a>");
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
			if (rel.indexOf("lightbox") != 0) {
				_parentA.attr("rel","lightbox");
			}
		}
	});

	if (!/android|iphone|ipod|series60|symbian|windows ce|blackberry/i.test(navigator.userAgent)) {
		$("a[rel^=\'lightbox\']").slimbox({imageFadeDuration:100,captionAnimationDuration:100}, null, function(el) {
			return (this == el) || ((this.rel.length > 8) && (this.rel == el.rel));
		});
	}
});
</script>
<!-- '.$lazyload_slideshow_vars["effect"].' end -->
');
	}

?>