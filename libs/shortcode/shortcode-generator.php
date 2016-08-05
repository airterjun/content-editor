<?php
function get_3_post_content($atts){

	extract(shortcode_atts(array(
		'class' => ''
	), $atts));

	$CPT = new CPT();

	$posts = $CPT->cptpost()->show($atts['select-post-col'])->get();

	$contentReturn = '';


	foreach ($posts as $key => $post){

		$contentReturn .= '<div class="featured-post-'.$atts['select-post-col'].'-box">
								<div class="inner-row"><h2>'.$post->post_title.'</h2></div>
								<div class="inner-row"><div class="inside-banner"><img src="'.$CPT->getFeatured($post->ID).'">"</div></div>
								<div class="inner-row"><div class="inside-content">'.$post->post_content.'</div></div>
							</div>';
	}


	return $contentReturn;


}


add_shortcode('post_content', 'get_3_post_content');