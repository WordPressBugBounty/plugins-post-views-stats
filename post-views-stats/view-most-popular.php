<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Prevent direct access
}

global $wpdb;
$table_name = $wpdb->prefix . "cn_track_post";

// Define limit safely (default 10 if not set)
$num = isset( $num ) ? intval( $num ) : 10;

// Build query safely
if ( isset( $_POST['from'] ) && isset( $_POST['to'] ) ) {

	// Sanitize input
	$from_date = sanitize_text_field( wp_unslash( $_POST['from'] ) );
	$to_date   = sanitize_text_field( wp_unslash( $_POST['to'] ) );

	// Use prepare for secure SQL
	$query = $wpdb->prepare(
		"SELECT post_id, COUNT(*) AS counts 
		 FROM $table_name 
		 WHERE create_date >= %s 
		   AND create_date <= %s 
		 GROUP BY post_id 
		 ORDER BY counts DESC 
		 LIMIT 0, %d",
		$from_date,
		$to_date,
		$num
	);

} else {

	// Safe static query (no user input)
	$query = $wpdb->prepare(
		"SELECT post_id, COUNT(*) AS counts 
		 FROM $table_name 
		 GROUP BY post_id 
		 ORDER BY counts DESC 
		 LIMIT 0, %d",
		$num
	);
}
// Get results
$tabledata = $wpdb->get_results( $query );
// Output list
echo '<ul>';
if ( ! empty( $tabledata ) ) {
	foreach ( $tabledata as $data ) {
		$post_id = intval( $data->post_id );
		$post    = get_post( $post_id );

		if ( ! $post ) {
			continue;
		}

		$title = get_the_title( $post_id );
		echo '<li><a href="' . esc_url( get_permalink( $post_id ) ) . '">' . esc_html( $title ? $title : __( '(No Title)', 'post-views-stats' ) ) . '</a></li>';
	}
} else {
	echo '<li>' . esc_html__( 'No posts found.', 'post-views-stats' ) . '</li>';
}
echo '</ul>';