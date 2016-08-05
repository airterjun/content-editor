<?php
/**
 * Created by PhpStorm.
 * User: iMac8
 * Date: 7/28/16
 * Time: 4:51 PM
 */

require_once KANNA_PLUGIN_DIR . '/views/content-type.php';
require_once KANNA_PLUGIN_DIR . '/functions/function-create-content-editor.php';
function my_scripts_method()
{
	wp_deregister_script('jquery-ui-core');
	wp_enqueue_script("jquery-ui-core", "//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.0/jquery-ui.js", false);
}

add_action('admin_enqueue_scripts', 'my_scripts_method');

function load_admin_scripts()
{
	wp_enqueue_style("gridster-style", "//cdnjs.cloudflare.com/ajax/libs/gridstack.js/0.2.5/gridstack.min.css", false);
	wp_enqueue_script("bootstrap", "//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js", false);
	wp_enqueue_script("lodash", "https://cdn.jsdelivr.net/lodash/4.14.1/lodash.min.js", false);
	wp_enqueue_script("gridstack-core", "//cdnjs.cloudflare.com/ajax/libs/gridstack.js/0.2.5/gridstack.min.js", false);

	wp_enqueue_style("editor-style", get_stylesheet_directory_uri() . "/libs/css/editor.css", false);
	wp_enqueue_script("editor-core", get_stylesheet_directory_uri() . "/libs/js/editor-core.js", false);
}

add_action("admin_enqueue_scripts", "load_admin_scripts");


function meta_box_markup($object)
{
	wp_nonce_field(basename(__FILE__), 'page-builder');
	$datas = get_post_meta($object->ID, 'complete_layout_data', true);

	$datasArray = json_decode($datas);

	$box_types = get_box_type();

	?>

	<div class="select-box-type">
		<ul>
			<?php

			foreach ($box_types as $key => $type) {

				$module_type = '';

				if($type['module_type']){
					$module_type = $type['module_type'];
				}

				echo '<li class="type" data-box-type="' . $key . '" data-module-type="'.$module_type.'">' . $type['title'] . '</li>';
			}
			?>
		</ul>
	</div>

	<a href="#" id="add-new-box">Add Box</a>
	<br>
	<input type="text" name="complete_layout_data" value="" id="complete_layout_data" style="width: 100%">

	<div style="background-color: grey; width: 900px" class="grid-stack">
		<?php

		foreach ($datasArray as $box) {

			$postId = '';

			if ($box->getPost) {
				$postId = $box->getPost;
			}

			echo '<div class="grid-stack-item"
						data-box-type="' . $box->type . '"
				        data-gs-x="' . $box->x . '" data-gs-y="' . $box->y . '"
				        data-content="' . $box->content . '" data-banner="' . $box->banner . '"
				        data-link-url="' . $box->url . '" data-attr-class="' . $box->attrClass . '"
				        data-bg-color="' . $box->bgColor . '"
				        data-post-id="' . $postId . '"
				        data-gs-width="' . $box->width . '" data-gs-height="' . $box->height . '">
				            <div class="grid-stack-item-content" style="background-color:' . urldecode($box->bgColor) . '">
				                <a href="#" class="edit-content">Edit</a>
				                <a href="#" class="delete-content">Delete</a>
				             </div>
				    </div>';
		}

		?>

	</div>
	

	<div id="visual-editor" class="visual-editor">

		<?php wp_editor("", "gridster_edit", array("tinymce" => true, 'media_buttons' => true)); ?>
		<div class="editor_container">

		</div>
		<?php
//
//			$post = new CPT();
//			echo '<option value="">-- Select post --</option>';
//			foreach ($post->cptpost()->get() as $post) {
//				echo '<option value="' . $post->ID . '">' . $post->post_title . '</option>';
//			}
//
//			?>
<!--		</select>-->
<!--		<br/>-->
		<a href="#" id="save-content">Save Content</a>
	</div>

	<?php

	require_once KANNA_PLUGIN_DIR . '/js/scripts.php';
//	require_once KANNA_PLUGIN_DIR . '/views/template.php';
}

function my_custom_meta_box()
{
	add_meta_box("page-builder", "Page Builder", "meta_box_markup", "page", "normal", "default", null);
}

add_action("add_meta_boxes", "my_custom_meta_box");


function save_my_custom_meta_box($post_id, $post, $update)
{
	if (!isset($_POST["page-builder"]) || !wp_verify_nonce($_POST["page-builder"], basename(__FILE__)))
		return $post_id;

	if (!current_user_can("edit_post", $post_id))
		return $post_id;

	if (defined("DOING_AUTOSAVE") && DOING_AUTOSAVE)
		return $post_id;

	$slug = "page";
	if ($slug != $post->post_type)
		return;


	$complete_layout_data = "";
	if (isset($_POST["complete_layout_data"])) {
		$complete_layout_data = $_POST["complete_layout_data"];
	} else {
		$complete_layout_data = "";
	}
	update_post_meta($post_id, "complete_layout_data", $complete_layout_data);
}

add_action("save_post", "save_my_custom_meta_box", 10, 3);


function page_builder_content_filter()
{

	global $post;

	$CPT = new CPT();

	$content = '';

	if ("page" == get_post_type()) {
		$builder_content = json_decode(get_post_meta($post->ID, 'complete_layout_data', true));

		$row = array();


		foreach ($builder_content as $value) {
			$row[] = $value->y;
		}

		$sections = array_unique($row);


		asort($sections);


		foreach ($sections as $section) {

			$content .= '<section id="section-' . $section . '" class="gridster-box-holder">';


			foreach ($builder_content as $key => $value) {


				$grid = $value->width / 12 * 100;
				$row = $value->y;
				$id = 'box-grid-' . $key;
				$contentText = '';
				$banner = '';
				$bgColor = '';
				$boxClass = ($value->box_class != 'undefined' ? $value->box_class : '');


				if ($value->banner) {
					$banner = 'background-image:url(' . urldecode($value->banner) . ')';
				}


				if ($value->bgColor) {
					$bgColor = 'background-color : ' . urldecode($value->bgColor);
				}


				if ($value->content) {
					$contentText = '<div class="grid-inner"><div class="grid-inside"><div class="content">' . $value->content . '</div></div></div>';
				}


				if ($row == $section) {

					$contentBox = '';

					if ($value->getPost) {

						$postData = $CPT->cptpost()->first($value->getPost);
						$contentPost = '<div class="grid-inner"><div class="grid-inside"><div class="content">' . $postData->post_content . '</div></div></div>';
						$bannerPost = 'background-image:url(' . $CPT->getFeatured($value->getPost) . ')';
						$contentBox .= "<div id='" . $id . "' class='gridster-box $boxClass' style='width : $grid%; $bannerPost; order: $value->x; $bgColor'>" . $contentPost . "</div>";

					} else {

						$contentBox .= "<div id='" . $id . "' class='gridster-box $boxClass' style='width : $grid%; $banner; order: $value->x; $bgColor'>" . urldecode($contentText) . "</div>";
					}


					$content .= do_shortcode($contentBox);
				}

			}


			$content .= '</section>';


		}

	}

	return $content;
}


function get_ajax_post()
{

	if (defined('DOING_AJAX') && DOING_AJAX) {

		$CPT = new CPT();

		$query = $_POST['datas'];


		$get_posts = $CPT->cptpost()->show($query[0]['limit'])->get();

		$content = array();

		foreach ($get_posts as $post) {

			$content[] = '<div class="grid-stack-item" data-box-type="post_content" 
						data-content="' . urlencode($post->post_content) . '"
						data-url="' . urlencode(get_the_permalink($post->ID)) . '"
						data-banner="' . urlencode($CPT->getFeatured($post->ID)) . '"
						>
						<div class="grid-stack-item-content" >
							<a href="#" class="edit-content">Edit</a>
						</div>
				 </div>';
		}


		echo json_encode($content);


	}

	die();

} // end theme_custom_handler

add_action('wp_ajax_nopriv_get_post', 'get_ajax_post');
add_action('wp_ajax_get_post', 'get_ajax_post');


function parsing_content()
{

	if (defined('DOING_AJAX') && DOING_AJAX) {

		$query = $_POST['datas'];

		global $post;

		$CPT = new CPT();

		$content = '';

		$builder_content = $query;

		$row = array();


		foreach ($builder_content as  $value) {

			$row[] = $value['y'];
		}

		$sections = array_unique($row);


		asort($sections);


		foreach ($sections as $section) {

			$content .= '<section id="section-' . $section . '" class="gridster-box-holder">';


			foreach ($builder_content as $key => $value) {


				$grid = $value['width']/ 12 * 100;
				$row = $value['y'];
				$id = 'box-grid-' . $key;
				$contentText = '';
				$banner = '';
				$bgColor = '';
				$shortCode = '';
				$boxClass = ($value['box_class']!= 'undefined' ? $value['box_class']: '');


				if ($value['banner']) {
					$banner = 'background-image:url(' . urldecode($value['banner']) . ')';
				}


				if ($value['bgColor']) {
					$bgColor = 'background-color : ' . urldecode($value['bgColor']);
				}


				if ($value['content']) {
					$contentText = '<div class="grid-inner"><div class="grid-inside"><div class="content">' . $value['content']. '</div></div></div>';
				}

				if( $value['shortCode'] ){
					$shortCode = $value['shortCode'];
				}


				if ($row == $section) {

					$contentBox = '';

					if ($value['getPost']) {

						$postData = $CPT->cptpost()->first($value['getPost']);
						$contentPost = '<div class="grid-inner"><div class="grid-inside"><div class="content">' . $postData->post_content . '</div></div></div>';
						$bannerPost = 'background-image:url(' . $CPT->getFeatured($value['getPost']) . ')';
						$contentBox .= "<div id='" . $id . "' class='gridster-box $boxClass' style='width : $grid%; $bannerPost; order: " . $value['x'] . "; $bgColor'>" . $contentPost . "</div>";

					} else {

						$contentBox .= "<div id='" . $id . "' class='gridster-box $boxClass' style='width : $grid%; $banner; order: " . $value["x"] . "; $bgColor'>" . urldecode(($shortCode ? $shortCode : $contentText)) . "</div>";
					}


					$content .= $contentBox;
				}

			}


			$content .= '</section>';


		}


		echo $content;

	}

	die();

} // end theme_custom_handler

add_action('wp_ajax_nopriv_data_parsing', 'parsing_content');
add_action('wp_ajax_data_parsing', 'parsing_content');



//add_filter("the_content", "page_builder_content_filter");