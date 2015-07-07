<?php
/*
Effect Name: prettyPhoto
Effect Version: 3.1.6
Effect URI: http://www.no-margin-for-errors.com/projects/prettyphoto-jquery-lightbox-clone
Description: prettyPhoto is a jQuery based lightbox clone. Not only does it support images, it also add support for videos, flash, YouTube, iFrames. It’s a full blown media lightbox. The setup is easy and quick, plus the script is compatible in every major browser.

Packager: Bruno Xu
Package URI: http://www.brunoxu.com/effects-for-images-lazyload-and-slideshow.html
Package Version: 1.0
*/

$available_effects['prettyPhoto 3.1.6'] = array(
	'name' => 'prettyPhoto 3.1.6',
	'folder_name' => '',
	'guide' => "prettyPhoto is a jQuery based lightbox clone. Not only does it support images, it also add support for videos, flash, YouTube, iFrame. It's a full blown media modal box. Please refer to http://www.no-margin-for-errors.com/projects/prettyPhoto/ for all the details on how to use.",
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
