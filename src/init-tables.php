<?php

function jal_install() {
	global $wpdb;

	$table_name = $wpdb->prefix . 'point_of_interest';
	
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		name tinytext NOT NULL,			/* Unique */
		lat double NOT NULL,
		lon double NOT NULL,
		PRIMARY KEY  (id)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
}

function jal_uninstall() {
	global $wpdb;

	$query = "DELETE FROM $wpdb->prefix" . "postmeta WHERE meta_key = '_location_name'";
	$wpdb->query($query);
	
	$query = "DROP TABLE $wpdb->prefix" . "point_of_interest";
	$wpdb->query($query);
}