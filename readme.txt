=== Images Lazyload and Slideshow ===
Contributors: xiaoxu125634
Donate link: http://blog.brunoxu.info/images-lazyload-and-slideshow/
Tags: Customized css for content images, True Lazyload, Images Slideshow Effect, Images View Effect, Tracking Code Setting
Requires at least: 3.0
Tested up to: 3.3
Stable tag: trunk

This plugin contains four gadgets: Customized css for content images, Images True Lazyload realization, Slideshow Effects using FancyBox or prettyPhoto etc, Tracking Code Setting.

== Description ==

This plugin is highly intelligent and useful, it contains four gadgets:

1. Customized css for content images, used to limit the image's max-width or add hover effect.
2. Images True Lazyload realization, all images on the web page will be lazyloaded.
3. Images slideshow effects.
4. Tracking code setting, no longer worry about missing the code during theme changes.

The four gadgets are all independent of each other, and can be enable and disabled separately.

Currently Support Effects:
FancyBox (adapters: two_galleries, one_gallery, single_image)
prettyPhoto (adapters: two_galleries, one_gallery, single_image)
slimbox2 (adapters: one_gallery, single_image)

For licence reason, this plugin only support effects under the GPL or MIT licence.

== Installation ==

This section describes how to install the plugin and get it working.

e.g.

1. Upload `images-lazyload-and-slideshow` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress Background.
3. Setting the plugin in 'Setting'->'Images Lazyload and Slideshow' page.


== Frequently Asked Questions ==

If there's any problem, Leave messages to me in page http://blog.brunoxu.info/images-lazyload-and-slideshow/


== Screenshots ==

1. /screenshot-1.jpg
2. /screenshot-2.jpg
3. /screenshot-3.jpg
4. /screenshot-4.jpg


== Changelog ==

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
