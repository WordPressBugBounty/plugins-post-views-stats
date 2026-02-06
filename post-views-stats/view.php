<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Prevent direct access
}

global $wpdb;
$table_name = $wpdb->prefix . "cn_track_post";

// Handle date range input securely
if ( isset( $_POST['from'] ) && isset( $_POST['to'] ) ) {

	// Sanitize input
	$from_date = sanitize_text_field( wp_unslash( $_POST['from'] ) );
	$to_date   = sanitize_text_field( wp_unslash( $_POST['to'] ) );

	// Use prepare with placeholders
	$query = $wpdb->prepare(
		"SELECT post_id, COUNT(*) as counts 
		 FROM $table_name 
		 WHERE create_date >= %s 
		   AND create_date <= %s 
		 GROUP BY post_id 
		 ORDER BY counts DESC 
		 LIMIT 0, 100",
		$from_date,
		$to_date
	);

} else {
	// Default query (no user input)
	$query = "SELECT post_id, COUNT(*) as counts 
			  FROM $table_name 
			  GROUP BY post_id 
			  ORDER BY counts DESC 
			  LIMIT 0, 100";
}

// Execute the query
$tabledata = $wpdb->get_results( $query );
?>
<div class="wrap">
	<h2><?php esc_html_e( 'Post Views Stats', 'post-views-stats' ); ?></h2>

	<div class="content_wrapper">
		<div class="left">
			<form action="" method="post">
				<p>
					<strong><?php esc_html_e( 'Date Range:', 'post-views-stats' ); ?></strong>
					<label for="from"><?php esc_html_e( 'From', 'post-views-stats' ); ?></label>&nbsp;
					<input type="text" id="from" name="from" maxlength="10" value="<?php echo isset( $from_date ) ? esc_attr( $from_date ) : ''; ?>" required />&nbsp;
					<label for="to"><?php esc_html_e( 'To', 'post-views-stats' ); ?></label>&nbsp;
					<input type="text" id="to" name="to" maxlength="10" value="<?php echo isset( $to_date ) ? esc_attr( $to_date ) : ''; ?>" required />&nbsp;
					<input type="submit" class="button-primary" value="<?php esc_attr_e( 'Submit', 'post-views-stats' ); ?>" />
				</p>
			</form>

			<?php if ( isset( $from_date ) && isset( $to_date ) ) : ?>
				<h3><?php echo esc_html( $from_date . ' - ' . $to_date ); ?></h3>
			<?php endif; ?>

			<table class="widefat page fixed" cellspacing="0">
				<thead>
					<tr valign="top">
						<th scope="col" width="50"><?php esc_html_e( 'Serial', 'post-views-stats' ); ?></th>
						<th scope="col" width="50"><?php esc_html_e( 'Post ID', 'post-views-stats' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Post Title', 'post-views-stats' ); ?></th>
						<th scope="col" width="100"><?php esc_html_e( 'Author', 'post-views-stats' ); ?></th>
						<th scope="col" width="70"><?php esc_html_e( 'Comments', 'post-views-stats' ); ?></th>
						<th scope="col" width="50"><?php esc_html_e( 'Views', 'post-views-stats' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					$i = 1;
					if ( ! empty( $tabledata ) ) :
						foreach ( $tabledata as $data ) :
							$posts = get_post( $data->post_id );
							if ( ! $posts ) {
								continue;
							}
							$title      = get_the_title( $posts );
							$user_info  = get_userdata( $posts->post_author );
							?>
							<tr valign="top">
								<td><?php echo intval( $i ); ?></td>
								<td>
									<a target="_blank" href="<?php echo esc_url( admin_url( 'post.php?post=' . intval( $data->post_id ) . '&action=edit' ) ); ?>">
										<?php echo intval( $data->post_id ); ?>
									</a>
								</td>
								<td>
									<a target="_blank" href="<?php echo esc_url( get_permalink( $data->post_id ) ); ?>">
										<?php echo esc_html( $title ? $title : __( '(No Title)', 'post-views-stats' ) ); ?>
									</a>
								</td>
								<td><?php echo esc_html( $user_info ? $user_info->user_login : __( 'Unknown', 'post-views-stats' ) ); ?></td>
								<td><?php echo intval( $posts->comment_count ); ?></td>
								<td><?php echo intval( $data->counts ); ?></td>
							</tr>
							<?php
							$i++;
						endforeach;
					else :
						?>
						<tr><td colspan="6"><?php esc_html_e( 'No data found for the selected range.', 'post-views-stats' ); ?></td></tr>
					<?php endif; ?>
				</tbody>
				<tfoot>
					<tr valign="top">
						<th width="50"><?php esc_html_e( 'Serial', 'post-views-stats' ); ?></th>
						<th width="50"><?php esc_html_e( 'Post ID', 'post-views-stats' ); ?></th>
						<th><?php esc_html_e( 'Post Title', 'post-views-stats' ); ?></th>
						<th width="100"><?php esc_html_e( 'Author', 'post-views-stats' ); ?></th>
						<th width="70"><?php esc_html_e( 'Comments', 'post-views-stats' ); ?></th>
						<th width="50"><?php esc_html_e( 'Views', 'post-views-stats' ); ?></th>
					</tr>
				</tfoot>
			</table>
		</div>
		<div class="right">
			<?php if ( function_exists( 'cn_tpv_admin_sidebar' ) ) { cn_tpv_admin_sidebar(); } ?>
		</div>
	</div>
</div>
