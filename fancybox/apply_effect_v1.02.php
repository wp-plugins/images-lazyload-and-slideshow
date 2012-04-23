<?php
/*
Name: Adapter for FancyBox Effect
Author: Bruno Xu 
Author URI: http://blog.brunoxu.info/
Version: 1.02
Comment: single image effect.
*/

	add_action('wp_footer', 'lazyload_slideshow_footer_effect');

	function lazyload_slideshow_footer_effect()
	{
		global $lazyload_slideshow_vars;

		print('
<!-- '.$lazyload_slideshow_vars["effect"].' -->
<script type="text/javascript" src="'.get_bloginfo('wpurl').'/wp-content/plugins/images-lazyload-and-slideshow/'.$lazyload_slideshow_vars["effect"].'/fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
<script type="text/javascript" src="'.get_bloginfo('wpurl').'/wp-content/plugins/images-lazyload-and-slideshow/'.$lazyload_slideshow_vars["effect"].'/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<link rel="stylesheet" type="text/css" href="'.get_bloginfo('wpurl').'/wp-content/plugins/images-lazyload-and-slideshow/'.$lazyload_slideshow_vars["effect"].'/fancybox/jquery.fancybox-1.3.4.css" media="screen" />
<script type="text/javascript">
jQuery(function($){
	$("#content img,.content img,.archive img,.post img").each(function(i){
		_self = $(this);

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
				_self.wrap("<a href=\'"+imgsrc+"\' class=\'fancyboxsingle\'></a>");
			}
		} else {
			aHref = this.parentNode.href.toLowerCase();
			var b=/(\.jpg$)|(\.jpeg$)|(\.png$)|(\.gif$)|(\.bmp$)/i;
			if (! b.test(aHref)) {
				return;
			}

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