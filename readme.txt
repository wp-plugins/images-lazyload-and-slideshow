=== Images Lazyload and Slideshow ===
Contributors: xiaoxu125634
Donate link: http://www.brunoxu.com/images-lazyload-and-slideshow.html
Tags: images lazyload, images slideshow effect, images popup view effect
Requires at least: 3.0
Tested up to: 3.9.1
Stable tag: trunk

This plugin is used to lazyload images on entire page, add popup view effect, slideshow effect to images.

== Description ==

This plugin is mainly use for:

1. images lazyload realization on entire page.
2. popup view effect, slideshow effect for images.
3. custom html setting, such as custom css for images and tracking codes etc.

These functions are all independent of each other, you can use them separately as you need.

Effects and adapters are easily to install and delete. You can install new effects or delete unuseful effects freely.

Native support effects: fancybox, prettyPhoto and slimbox2

Get more effects, please visit <a href="http://www.brunoxu.com/effects-for-images-lazyload-and-slideshow.html" target="_blank">http://www.brunoxu.com/effects-for-images-lazyload-and-slideshow.html</a>

== Installation ==

This section describes how to install the plugin and get it working.

e.g.

1. Upload `images-lazyload-and-slideshow` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress Background.
3. Setting the plugin in 'Setting'->'Images Lazyload and Slideshow' page.


== Frequently Asked Questions ==

If there's any question, leave a messages to me on page http://www.brunoxu.com/images-lazyload-and-slideshow.html


== Screenshots ==

1. /screenshot-1.jpg
2. /screenshot-2.jpg
3. /screenshot-3.jpg
4. /screenshot-4.jpg


== Changelog ==

= 3.0 =
* 2014-06-19
* 	upgrade : upgrade effect and adapter interface, easily to add, delete and develop.
* 	upgrade : every effect added into system as a plugin, easily to install or delete on the backend.
* 	upgrade : optimize lazyload script, not to load images far from current screen above.
* 	upgrade : combine "custom css" and "tracking code" into "custom html".
* 	upgrade : add lazyload scope config, all images or images in post content for choice.
* 	upgrade : optimize display of config page, define width in percentage.
* 	upgrade : change links of plugin homepage and author homepage.
* 	upgrade : upgrade slimbox2 version from 2.04 to 2.05

= 2.4.2 =
* 2012-10-10
* 	fixbug : fix 4.1.13_gallery_js effect's bug.

= 2.4.1 =
* 2012-09-26
* 	upgrade : optimize effect

= 2.4 =
* 2012-09-26
* 	upgrade : update the sample css for width limit and slideshow images' cursor style.
* 	upgrade : add diff feature for lazyload.
* 	upgrade : update the image's width and height get method.

= 2.3.1 =
* 2012-09-24
* 	upgrade : optimize effect

= 2.3 =
* 2012-09-24
* 	upgrade : add slimbox2 effect, now has two adapters:'one gallery' and 'sigle image'.

= 2.2.1 =
* 2012-07-17
* 	upgrade : optimize effect

= 2.2 =
* 2012-07-17
* 	upgrade : add exception for plugin WP-PostRatings, for displaying reason.
* 	upgrade : do not lazyload images for feeds, previews, mobile. refer to Lazy Load plugin.

= 2.1.1 =
* 2012-06-07
* 	upgrade : optimize effect

= 2.1 =
* 2012-06-07
* 	upgrade : for better performance, images with width or height use blank_1x1.gif as placeholder, while images without width and height use blank_250x250.gif as placeholder(except: smilies)
* 	upgrade : add a loading.gif background to each image, if image's loading is timeout, visitors will understand what happened.

= 2.0.1 =
* 2012-06-06
* 	upgrade : optimize effect

= 2.0 =
* 2012-06-06
* 	upgrade : expand the scope of lazyload. previously only the content images take effect, now all the images work.
* 	upgrade : expand the scope of slideshow, optimize the regexp check rule for images.

= 1.4.1 =
* 2012-05-29
* 	upgrade : optimize effect

= 1.4 =
* 2012-05-29
* 	upgrade : optimize lazyload, reduce the Performance Loss
* 	upgrade : optimize limit_width_selector and add_effect_selector, change to "#content img,.content img,.archive img,.post img,.page img"

= 1.3.1 =
* 2012-05-28
* 	upgrade : optimize effect

= 1.3 =
* 2012-05-28
* 	upgrade : user can customize add-effect-selector
* 	upgrade : user can choose which action to be hook, wp_footer or wp_head
* 	upgrade : change style wrap for config page, add min-width

= 1.2.2 =
* 2012-05-20
* 	upgrade : optimize effect

= 1.2.1 =
* 2012-05-18
* 	upgrade : optimize effect

= 1.2 =
* 2012-05-16
* 	fixbug : css of images' max-width limitation for IE6 may cause browser crash

= 1.1 =
* 2012-05-08
* 	upgrade : add adapters for effect, configurable
* 	upgrade : add special class 'slideshow_imgs' to every effected images
* 	upgrade : change the blank image to loading gif
* 	upgrade : setting page style adjust

= 1.0 =
* 2012-04-23 plugin released.
