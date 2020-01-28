<?php
function gut_insert_map_block() {

    global $wpdb;
    $table_name = $wpdb->prefix . 'point_of_interest';
    $locationList = $wpdb->get_results($wpdb->prepare("SELECT * from $table_name"));

    wp_register_script(
        'map-gut-block',
        THIS_DIR_URL . 'src/map-gutenberg-block.js',
        array( 'wp-blocks', 'wp-element' )
    );
    
    wp_localize_script(
        'map-gut-block',
        'Geopositions',
        ['geoList' => $locationList]
    );

    register_block_type( 'gut/map-block', array(
        
        'icon' => 'dashicons-location-alt',
        'render_callback' => 'gut_render_map',
        'editor_script' => 'map-gut-block'
    ));
}

function gut_render_map($params = null) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'point_of_interest';
    $locationList = $wpdb->get_results($wpdb->prepare("SELECT * from $table_name"));
    foreach ($locationList as $location){
        $location->coordinates = array(doubleval($location->lon), doubleval($location->lat));
    }
    $Vdata = include(THIS_DIR . 'templates' . DS . 'map-gutenberg-block.php');
    return $Vdata;
}