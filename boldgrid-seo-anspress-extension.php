<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link http://www.boldgrid.com
 * @since 1.0.0
 *
 * Plugin Name: BoldGrid SEO Anspress Extension
 * Plugin URI: http://www.boldgrid.com
 * Description: Anspress compatibility extension for BoldGrid SEO
 * Version: 1.0.1
 * Author: BoldGrid.com <wpb@boldgrid.com>
 * Author URI: http://www.boldgrid.com
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: bgseo-anspress
 * Domain Path: /languages
 */

/**
 * Initialize extension check.
 *
 * @since 1.0.0
 */
function bgseo_anspress_extension() {
	if ( ! class_exists( 'Boldgrid_Seo' ) ) {
		add_action( 'admin_notices', 'bgseo_anspress_error' );
		deactivate_plugins( 'boldgrid-seo-anspress-extension/boldgrid-seo-anspress-extension.php' );
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}
	}
}

add_action( 'admin_init', 'bgseo_anspress_extension' );

/**
 * Generates the admin notice if the BoldGrid SEO plugin is not
 * installed and activated.
 *
 * @since 1.0.0
 */
function bgseo_anspress_error() {
	?>
	<div class="notice notice-error is-dismissible">
		<p>
			<?php echo esc_html( 'The BoldGrid SEO Anspress Extension requires that the BoldGrid SEO plugin is installed and active!', 'bgseo-anspress' ); ?>
		</p>
	</div>
	<?php
}

/**
 * Removes BoldGrid SEO filters from wp_head that conflict.
 *
 * @since 1.0.0
 */
function bgseo_anspress_remove_seo_meta() {
	if ( function_exists( 'is_question' ) && is_question() ) {
		remove_all_actions( 'boldgrid-seo/seo/description' );
		remove_all_actions( 'boldgrid-seo/seo/canonical' );
		remove_all_actions( 'boldgrid-seo/seo/robots' );
		remove_all_actions( 'boldgrid-seo/seo/og:title' );
		remove_all_actions( 'boldgrid-seo/seo/og:site_name' );
		remove_all_actions( 'boldgrid-seo/seo/og:type' );
		remove_all_actions( 'boldgrid-seo/seo/og:url' );
		remove_all_actions( 'boldgrid-seo/seo/og:description' );
	}
}

add_action( 'wp_head', 'bgseo_anspress_remove_seo_meta', 0 );

/**
 * Gets the Anspress title.
 *
 * @since  1.0.0
 *
 * @param  int    $post_id WordPress Post ID.
 *
 * @return string $title   Modified title.
 */
function bgseo_anspress_title( $post_id ) {
	global $post;
	$post = get_post( $post_id );
	setup_postdata( $post );
	$title = get_the_title();
	wp_reset_postdata();

	return $title;
}

add_filter( 'pre_get_document_title', 'bgseo_anspress_add_title', 100 );

/**
 * Adds the anspress modified title to the <title> of the post.
 *
 * @since  1.0.0
 *
 * @param  string $title The title of the page.
 *
 * @return string $title The modified title of the page.
 */
function bgseo_anspress_add_title( $title ) {
	if ( function_exists( 'is_question' ) && is_question() ) {
		return bgseo_anspress_title( get_question_id() ) . ' | ' . get_bloginfo( 'blogname' );
	}

	return $title;
}

/**
 * Gets the anspress description for post.
 *
 * @since  1.0.0
 *
 * @param  int    $post_id WordPress Post ID.
 *
 * @return string $excerpt The anspress excerpt for post.
 */
function bgseo_anspress_description( $post_id ) {
	global $post;
	$post = get_post( $post_id );
	setup_postdata( $post );
	$excerpt = get_the_excerpt();
	wp_reset_postdata();

	return $excerpt;
}

add_action( 'wp_head', 'bgseo_anspress_add_description' );

/**
 * Adds the BoldGrid SEO description to <meta name="description>
 * for questions.
 *
 * @since 1.0.0
 */
function bgseo_anspress_add_description() {
	if ( function_exists( 'is_question' ) && is_question() ) {
		echo '<meta name="description" content="'. bgseo_anspress_description( get_question_id() ).'"/>';
	}
}
