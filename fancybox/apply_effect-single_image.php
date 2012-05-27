<?php
/*
Name: Adapter for FancyBox Effect
Author: Bruno Xu 
Author URI: http://blog.brunoxu.info/
Version: single_image
Comment: single image effect.
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
<script type="text/javascript" src="'.get_bloginfo('wpurl').'/wp-content/plugins/images-lazyload-and-slideshow/'.$lazyload_slideshow_vars["effect"].'/fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
<script type="text/javascript" src="'.get_bloginfo('wpurl').'/wp-content/plugins/images-lazyload-and-slideshow/'.$lazyload_slideshow_vars["effect"].'/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<link rel="stylesheet" type="text/css" href="'.get_bloginfo('wpurl').'/wp-content/plugins/images-lazyload-and-slideshow/'.$lazyload_slideshow_vars["effect"].'/fancybox/jquery.fancybox-1.3.4.css" media="screen" />
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
				_self.wrap("<a href=\'"+imgsrc+"\' class=\'fancyboxsingle\'></a>");
			}
		} else {
			aHref = this.parentNode.href.toLowerCase();
			var b=/(\.jpg$)|(\.jpeg$)|(\.png$)|(\.gif$)|(\.bmp$)/i;
			if (! b.test(aHref)) {
				return;
			}

			_self.addClass("slideshow_imgs");

			_parentA = $(this.parentNode);
			_parentA.addClass("fancyboxsingle");
		}
	});

	$("a.fancyboxsingle").fancybox({
		\'overlayShow\'		: true,
		\'hideOnContentClick\'		: true,
		\'transitionIn\'		: \'elastic\',
		\'transitionOut\'		: \'elastic\'
	});
});
</script>
<!-- '.$lazyload_slideshow_vars["effect"].' end -->
');
	}

?>