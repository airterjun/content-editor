<?php

/**
 * Created by PhpStorm.
 * User: iMac8
 * Date: 7/28/16
 * Time: 2:52 PM
 */


class KANNA
{


	use template;


	/*
	 * Select post for showing the editor lib
	 * Default Post and Page
	 */
	public $show_on  = array('post', 'page');

	public $editor_id = 'kanna_editor';


	public function __construct(){

		add_action( 'admin_init', array( $this, 'register_meta'), 10 );
		add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ) );

	}

	public function register_meta(){

		foreach ($this->show_on as $post){
			add_meta_box(
				$this->editor_id, __( KANNA_LIB_NAME, 'textdomain' ), array(__CLASS__, 'screen'),
				$post,
				'normal',
				'default'
			);

		}
	}


	public function screen(){

		?>

			<a href="#" id="add-new-element" class="button-kanna-green">Add Element</a>


			<div id="kanna-editor-container">

			</div>
		<?php

		require_once KANNA_PLUGIN_DIR . '/functions/scripts.php';

	}


	public function scripts(){


		foreach ($this->scripts_to_register() as $handle=>$script){

			wp_enqueue_script($handle, get_base_lib_url() . '/' . $script);
		}


	}



	public function scripts_to_register(){

		$scripts = array();

		$scripts['core-editor'] = 'js/editor-core.js';
		$scripts['templates'] = 'js/templates.js';

		return $scripts;


	}

}

$kanna = new KANNA();