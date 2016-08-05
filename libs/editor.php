<?php
/*
 * Author : Edy Saputra
 * Project Created : July 28, 2016
 * Version : 1.0
 * Used by : Kesato & Co
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( "KANNA_LIB_NAME",__("Content Editor","" ));
define( 'KANNA_PLUGIN', __FILE__ );
define( 'KANN_PLUGIN_BASENAME', plugin_basename( KANNA_PLUGIN ) );
define( 'KANNA_PLUGIN_DIR', untrailingslashit( dirname( KANNA_PLUGIN ) ) );

define( "KANNA_VERSION" ,"1.0");


/*
 * Include editor.php from your functions.php
 * Call init function from
 */


function editor_init(){

	@include 'functions/register-screen-editor.php';

}


editor_init();
