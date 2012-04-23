<?php
/*
Name: Adapter for Fancy Zoom Effect
Author: Bruno Xu 
Author URI: http://blog.brunoxu.info/
Version: 1.0
Comment: single image view effect, do not support gallery effect.
*/

	add_action('wp_footer', 'lazyload_slideshow_footer_effect');

	function lazyload_slideshow_footer_effect()
	{
		global $lazyload_slideshow_vars;

		print('
<!-- '.$lazyload_slideshow_vars["effect"].' -->
<script src="'.get_bloginfo('wpurl').'/wp-content/plugins/images-lazyload-and-slideshow/'.$lazyload_slideshow_vars["effect"].'/js-global/FancyZoom.js" type="text/javascript"></script>
<script src="'.get_bloginfo('wpurl').'/wp-content/plugins/images-lazyload-and-slideshow/'.$lazyload_slideshow_vars["effect"].'/js-global/FancyZoomHTML.js" type="text/javascript"></script>
<script type="text/javascript">
jQuery(function($){
	$("#content img,.content img,.archive img,.post img").each(function(i){
		if (! this.parentNode.href) {
			_self = $(this);

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
				_self.wrap("<a href=\'"+imgsrc+"\'></a>");
			}
		}
	});

	zoomImagesURI = "'.get_bloginfo('wpurl').'/wp-content/plugins/images-lazyload-and-slideshow/'.$lazyload_slideshow_vars["effect"].'/images-global/zoom/";
	setupZoom();
});
</script>
<!-- '.$lazyload_slideshow_vars["effect"].' end -->
');
	}

?>