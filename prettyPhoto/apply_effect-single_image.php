<?php
/*
Name: Adapter for prettyPhoto Effect
Author: Bruno Xu 
Author URI: http://blog.brunoxu.info/
Version: single_image
Comment: single effect.
*/

	add_action($lazyload_slideshow_vars["use_footer_or_head"], 'lazyload_slideshow_footer_effect');

	function lazyload_slideshow_footer_effect()
	{
		global $lazyload_slideshow_vars;

		if (! $lazyload_slideshow_vars["add_effect_selector"]) {
			return;
		}

		print('
<!-- '.$lazyload_slideshow_vars["effect"].' -->
<link rel="stylesheet" href="'.get_bloginfo('wpurl').'/wp-content/plugins/images-lazyload-and-slideshow/'.$lazyload_slideshow_vars["effect"].'/css/prettyPhoto.css" type="text/css" media="screen" charset="utf-8"/>
<script type="text/javascript" src="'.get_bloginfo('wpurl').'/wp-content/plugins/images-lazyload-and-slideshow/'.$lazyload_slideshow_vars["effect"].'/js/jquery.prettyPhoto.js" charset="utf-8"></script>
<script type="text/javascript">
jQuery(function($){
	$("'.$lazyload_slideshow_vars["add_effect_selector"].'").each(function(i){
		_self = $(this);

		if ((_self.width() && _self.width()<50)
				|| (_self.height() && _self.height()<50)) {
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
				_self.wrap("<a href=\'"+imgsrc+"\' rel=\'prettyPhoto\'></a>");
			}
		} else {
			aHref = this.parentNode.href.toLowerCase();
			var b=/(\.jpg$)|(\.jpeg$)|(\.png$)|(\.gif$)|(\.bmp$)/i;
			if (! b.test(aHref)) {
				return;
			}

			_self.addClass("slideshow_imgs");

			_parentA = $(this.parentNode);
			rel = _parentA.attr("rel");
			if (! rel) {
				rel = "";
			}
			if (rel.indexOf("prettyPhoto") != 0) {
				_parentA.attr("rel","prettyPhoto");
			}
		}
	});

	$("a[rel^=\'prettyPhoto\']").prettyPhoto({social_tools:""});
});
</script>
<!-- '.$lazyload_slideshow_vars["effect"].' end -->
');
	}

?>