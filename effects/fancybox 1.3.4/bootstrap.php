<?php
/*
Effect Name: fancybox
Effect Version: 1.3.4
Effect URI: http://fancybox.net/
Description: FancyBox is a tool for displaying images, html content and multi-media in a Mac-style "lightbox" that floats overtop of web page.

Packager: Bruno Xu
Package URI: http://www.brunoxu.com/effects-for-images-lazyload-and-slideshow.html
Package Version: 1.0
*/

$available_effects['fancybox 1.3.4'] = array(
	'name' => 'fancybox 1.3.4',
	'folder_name' => '',
	'guide' => 'FancyBox is a tool for displaying images, html content and multi-media in a Mac-style "lightbox" that floats overtop of web page. visit http://fancybox.net/ to learn more.',
	'package_version' => '1.0',
	'adapters' => array(
		"gallery" => array(
			'name' => 'gallery',
			'file_name' => '',
			'guide' => 'add all images into a gallary use js',
			'version' => '1.0',
		),
		"single" => array(
			'name' => 'single',
			'file_name' => '',
			'guide' => 'each image has a single popup view box, not a gallary view',
			'version' => '1.0',
		),
		"two_galleries" => array(
			'name' => 'two_galleries',
			'file_name' => '',
			'guide' => 'generate two galleries, images has href and the other has not',
			'version' => '1.0',
		),
	),
);
