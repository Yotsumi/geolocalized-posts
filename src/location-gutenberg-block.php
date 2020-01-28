<?php
function gut_insert_location_block() {

    global $wpdb;
    $table_name = $wpdb->prefix . 'point_of_interest';
    $locationList = $wpdb->get_results($wpdb->prepare("SELECT * from $table_name"));

    wp_register_script(
        'loc-gut-block',
        THIS_DIR_URL . 'src/location-gutenberg-block.js',
        array( 'wp-blocks', 'wp-element' )
    );
    
    wp_localize_script(
        'loc-gut-block',
        'Geopositions',
        ['geoList' => $locationList]
    );

    register_block_type( 'gut/location-block', array(
        
        'icon' => 'dashicons-location',
        'render_callback' => 'gut_render_loc_post',
        'editor_script' => 'loc-gut-block'
    ) );
}

function gut_render_loc_post($params) {
    if ($params['nameTag'] == '') return;
    $locationName = $params['nameTag'];
    $lastLocationName = $params['lastNameTag'];
    if ($lastLocationName == '' || $lastLocationName == null)
        $lastLocationName = '-Gnente-';
    $locationLon = $params['lon'];
    $locationLat = $params['lat'];
    $id = get_the_ID();
    
    $metaDataArray = get_post_meta($id, '_location_name');
    $status = get_post_status();
    if ($status != 'publish' && $status != 'draft'){
        $insert = true;
        foreach ($metaDataArray as $metaData){
            if ($metaData == $locationName)
                $insert = false;
        }
        if ($insert)
            add_post_meta( $id, '_location_name', $locationName );
    }
    
    if ($status == 'publish'){
        $insert = true;
        foreach ($metaDataArray as $metaData){
            if ($metaData == $locationName)
                $insert = false;
        }
        if ($insert){
            $updated = update_post_meta( $id, '_location_name', $locationName, $lastLocationName );
            if (!$updated)
                add_post_meta( $id, '_location_name', $locationName );
        }
    }
    $Vdata = include(THIS_DIR . 'templates' . DS . 'location-post.php');
    return $Vdata;
}

function is_updated($idPost, $locationName){
    global $wpdb;
    $table_name = $wpdb->prefix . 'point_of_interest';
    $table_name_rel = $wpdb->prefix . 'poi_rel';
    $table_name_post = $wpdb->prefix . 'posts';
    
    $hasPoi = $wpdb->get_results($wpdb->prepare("SELECT * from $table_name_rel por join $table_name_post post on post.id = por.id_articolo join $table_name po on po.id = por.id_poi where post.id = $idPost and po.name = '$locationName'"));
    return empty($hasPoi);
}
