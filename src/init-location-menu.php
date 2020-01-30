<?php

function wpdocs_register_menu_page(){
    add_menu_page( 
        __( 'Location Manager', 'textdomain' ),
        'Location List',
        'manage_options',
        'custompage',
        'location_manager_menu_page',
        'dashicons-location'
    ); 

    add_submenu_page( 
        'custompage',
        'Settings',
        'Settings',
        'manage_options',
        'settings_menu_page',
        'settings_location_menu_page',
        1
    ); 
}

add_action( 'admin_menu', 'wpdocs_register_menu_page' );
 
function location_manager_menu_page(){

    global $wpdb;
    $table_name = $wpdb->prefix . 'point_of_interest';
    $locationList = $wpdb->get_results($wpdb->prepare("SELECT * from $table_name"));
    foreach ($locationList as $location){
        $location->coordinates = array(doubleval($location->lon), doubleval($location->lat));
    }
    
    include_once(THIS_DIR . 'templates' . DS . 'location-list-menu.php');
}

function settings_location_menu_page(){
    $my_query = new WP_Query(array('post_type'=>'post', 'post_status'=>'publish', 'posts_per_page'=>-1));

    var_dump($my_query);
}

add_action( 'wp_ajax_set_poi', 'set_poi' );
add_action( 'wp_ajax_delete_poi', 'delete_poi' );

function set_poi() {
	global $wpdb;
    $table_name = $wpdb->prefix . 'point_of_interest';
	$data = array('lon' => $_POST['lon'], 'lat' => $_POST['lat'], 'name' => $_POST['tagName']);
    $wpdb->insert($table_name,$data);
	wp_die(); // this is required to terminate immediately and return a proper response
}

function delete_poi() {
    global $wpdb;
    $tag = $_POST['tag'];
    $query = "DELETE FROM $wpdb->prefix" . "point_of_interest WHERE name = '$tag'";
    $wpdb->query($query);
    $query = "DELETE FROM $wpdb->prefix" . "postmeta WHERE meta_value = '$tag'";
    $wpdb->query($query);
	wp_die(); // this is required to terminate immediately and return a proper response
}