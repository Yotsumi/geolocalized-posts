<?php

function sorter(){
    if ($_GET['sort'] == 'near' && $_GET['lon'] && $_GET['lat']){
        add_filter( 'posts_clauses', 'filter_by_location', 10, 2 );
    }
    if ($_GET['sort'] == 'locationName' && $_GET['locName']){
        add_filter( 'posts_clauses', 'filter_by_location_name', 10, 2 );
    }
}

function filter_by_location( $clauses, $query_object ){
    global $wpdb;

    if ( $query_object->is_home() ){
    
        $join = &$clauses['join'];
        if (! empty( $join ) ) $join .= ' ';
        $join .= " JOIN {$wpdb->prefix}postmeta pm ON pm.post_id = {$wpdb->prefix}posts.ID JOIN {$wpdb->prefix}point_of_interest po on po.name = pm.meta_value";
    
        $orderby = &$clauses['orderby'];
        $sf = 3.14159 / 180;
        $lat = doubleval($_GET['lat']);
        $lon = doubleval($_GET['lon']);
        $orderby = "ACOS(SIN(lat*$sf)*SIN($lat*$sf) + COS(lat*$sf)*COS($lat*$sf)*COS((lon-$lon)*$sf))";
    }

  return $clauses;
}

function filter_by_location_name( $clauses, $query_object ){
    global $wpdb;

    if ( $query_object->is_home() ){
    
        $join = &$clauses['join'];
        if (! empty( $join ) ) $join .= ' ';
        $join .= " JOIN {$wpdb->prefix}postmeta pm ON pm.post_id = {$wpdb->prefix}posts.ID JOIN {$wpdb->prefix}point_of_interest po on po.name = pm.meta_value";
        $locName = $_GET['locName'];
        $clauses['where'] .= " AND po.name = '$locName'";

    }

  return $clauses;
}



// SELECT * FROM wptest.wp_posts
// WHERE id IN (234, 232) 
// ORDER BY FIELD(id,234, 232)