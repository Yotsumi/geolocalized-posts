<?php

include(plugin_dir_path( __FILE__ ) . 'Sorting' . DS . 'Sorting.php');
include(plugin_dir_path( __FILE__ ) . 'Sorting' . DS . 'PilloleForSorting.php');

function sorter(){
    if ($_GET['action'] == 'sort' && isset($_GET['lon']) && isset($_GET['lat']) && isset($_GET['distanceWeight']) && isset($_GET['timeWeight'])){
        add_filter( 'posts_clauses', 'filter_by_location', 10, 2 );
    }
}

function filter_by_location( $clauses, $query_object ){
    global $wpdb;

    if ( $query_object->is_home() ){
        $ps = new pilloleSorting;

        $query = 'SELECT p.ID, poi.lat, poi.lon, p.post_date_gmt FROM ((wp_posts as p INNER JOIN wp_postmeta as pom on pom.post_id = p.ID) INNER JOIN wp_point_of_interest as poi on poi.name = pom.meta_value)';
        $pilloleFromDB = [];

        $pilloleFromDB = $wpdb->get_results($query);

        $pillole = [];
        
        for ($i=0; $i < sizeof($pilloleFromDB); $i++) { 
            array_push($pillole, 
            new PilloleForSorting(
                intval($pilloleFromDB[$i]->ID), 
                floatval($pilloleFromDB[$i]->lat), 
                floatval($pilloleFromDB[$i]->lon), 
                $pilloleFromDB[$i]->post_date_gmt));
        }

        $pilloleSorted = $ps->SortingByWeight($pillole, floatval($_GET['lat']), floatval($_GET['lon']), floatval($_GET['distanceWeight']), floatval($_GET['timeWeight']));

        // clauses WHERE
        $ids = "";
        foreach ($pilloleSorted as $id) {
            $ids .= $id . ", ";
        }
        $ids = substr($ids, 0, strlen($ids) - 2);

        $clauses['where'] .= " AND id IN ( $ids )";

        // clauses ORDERBY

        $clauses['orderby'] = " FIELD( ID, $ids )";
    }

  return $clauses;
}