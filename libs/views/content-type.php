<?php
function get_box_type(){
	$box_types = array();

	$box_types['text_w_bg'] = array(
		'title' => 'Text with background',
		'icon' => ''
	);

	$box_types['img'] = array(
		'title' => 'Image',
		'icon' => ''
	);


	$box_types['link_text_w_bg'] = array(
		'title' => 'Link text with background',
		'icon' => ''
	);

	$box_types['text'] = array(
		'title' => 'Text',
		'icon' => ''
	);


	$box_types['post_three_col'] = array(
		'title' => 'Three column post',
		'icon' => '',
		'module_type' => 'shortcode'
	);


	$box_types['post_single'] = array(
		'title' => 'Single Post',
		'icon' => '',
		'module_type' => 'shortcode'
	);


	return  $box_types;
}