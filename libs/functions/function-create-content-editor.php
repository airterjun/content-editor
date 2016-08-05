<?php

function ajax_content_editor()
{

	if (defined('DOING_AJAX') && DOING_AJAX) {

		$module = $_POST['module'];


		if( isset($module) ){

			if( $module == 'text' ){
				handle_text_editor();
			}

			if( $module == 'img' ){
				handle_image_editor();
			}

			if( $module == 'post_content' ){
				handle_post_editor();
			}
		}


	}

	die();

}

add_action('wp_ajax_nopriv_content_editor', 'ajax_content_editor');
add_action('wp_ajax_content_editor', 'ajax_content_editor');


function handle_text_editor(){
	?>
		<input type="text" class="field-editor" id="box-attr-class" value="" placeholder="Custom box class">
		<input type="text" class="field-editor" id="box-link-url" value="" placeholder="Url">
		<input type="text" class="field-editor" id="box-bg-color" value="" placeholder="Background Color">
	<?php
}



function handle_image_editor(){
	?>
	<input type="text" class="field-editor" id="box-attr-class" value="" placeholder="Custom box class">
	<input type="text" class="field-editor" id="box-link-url" value="" placeholder="Url">
	<input type="text" class="field-editor" id="box-banner" value="" placeholder="Image Url">
	<?php
}


function handle_post_editor(){
	?>
	<select id="select-post-col" class="field-editor">
		<option value="1">1</option>
		<option value="2">2</option>
		<option value="3">3</option>
		<option value="4">4</option>
	</select>

	<input type="text" class="field-editor" id="box-attr-class" value="" placeholder="Custom box class">
	<input type="text" class="field-editor" id="box-link-url" value="" placeholder="Url">
	<input type="text" class="field-editor" id="box-bg-color" value="" placeholder="Background Color">
	<?php
}