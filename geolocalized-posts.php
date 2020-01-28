<?php
/**
 * Plugin Name: Geolocalized Posts
 * Plugin URI: http://www.mywebsite.com/geolocalized-posts
 * Description: A plugin for manage and associate geotag to posts
 * Version: 2.0
 * Author: Yotsumi
 */

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
define( 'THIS_DIR', plugin_dir_path( __FILE__ ) );
define( 'THIS_DIR_URL', plugin_dir_url( __FILE__ ) );
define( 'DS', DIRECTORY_SEPARATOR );

require_once( THIS_DIR . 'src' . DS . 'init-tables.php' );
require_once( THIS_DIR . 'src' . DS . 'init-location-menu.php' );
require_once( THIS_DIR . 'src' . DS . 'location-gutenberg-block.php');
require_once( THIS_DIR . 'src' . DS . 'map-gutenberg-block.php');
require_once( THIS_DIR . 'src' . DS . 'post-sorter.php');

sorter();

register_activation_hook( __FILE__, 'jal_install' );
add_action( 'init', 'gut_insert_location_block' );
add_action( 'init', 'gut_insert_map_block' );

add_action( 'wp_ajax_my_action', 'my_action_callback' );

add_shortcode( 'mapForGeotags', 'gut_render_map' );
