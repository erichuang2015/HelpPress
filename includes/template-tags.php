<?php
/**
 * Template Tags
 *
 * @package HelpPress
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Returns a Genericon SVG snippet.
 *
 * @since 1.0.0
 *
 * @param string $icon Icon name.
 * @param integer $size Icon size in pixels.
 * @return string SVG icon code.
 */
if ( ! function_exists( 'helppress_genericon' ) ) :
function helppress_genericon( $icon, $size = 16 ) {

	$svg_url = esc_url( HELPPRESS_URL . '/assets/img/genericons-neue.svg' );

	return "<svg class='helppress-genericon helppress-genericon--{$icon} helppress-genericon--{$size}' width='{$size}px' height='{$size}px'><use xmlns:xlink='http://www.w3.org/1999/xlink' xlink:href='{$svg_url}#hp-genericon-{$icon}'></use></svg>";

}
endif;

/**
 * Wrapper for article queries. Applies query args as configured in admin Settings.
 *
 * @since 1.0.0
 *
 * @param array $args Additional `WP_Query` args to apply to the article query.
 * @return object `WP_Query` object.
 */
function helppress_get_articles( $args = array() ) {

	$args = wp_parse_args( $args, array(
		'post_type'      => 'hp_article',
		'orderby'        => helppress_get_option( 'orderby' ),
		'order'          => helppress_get_option( 'order' ),
		'posts_per_page' => helppress_get_option( 'posts_per_page' ),
		'paged'          => helppress_page_num(),
	) );

	$args = apply_filters( 'helppress_get_articles_args', $args );

	$articles = new WP_Query( $args );

	return apply_filters( 'helppress_get_articles', $articles );

}

/**
 * Returns breadcrumb trail data.
 *
 * @since 1.0.0
 *
 * @return array Breadcrumb trail.
 */
function helppress_get_breadcrumb() {

	$breadcrumb = new HelpPress_Breadcrumb();

	return $breadcrumb->get_trail();

}

/**
 * Helper for category query. Applies query args as configured in admin Settings.
 *
 * @since 1.0.0
 *
 * @param array $args Additional `get_terms()` args to apply to query.
 * @return array `get_terms()` result.
 */
function helppress_get_categories( $args = array() ) {

	$args = wp_parse_args( $args, array(
		'taxonomy' => 'hp_category',
		'orderby'  => 'menu_order',
	) );

	$args = apply_filters( 'helppress_get_categories_args', $args );

	$terms = get_terms( $args );

	return apply_filters( 'helppress_get_categories', $terms );

}

/**
 * Outputs category article count.
 *
 * @see 1.2.1
 *
 * @param integer|object $category Category ID or WP_Term object.
 */
if ( ! function_exists( 'helppress_category_article_count' ) ) :
function helppress_category_article_count( $category ) {

	if ( ! is_a( $category, 'WP_Term' ) ) {
		$category = get_term( $category );
	}

	$count   = array();
	$count[] = '<span>';
	$count[] = number_format_i18n( $category->count );
	$count[] = '</span>';
	$count   = join( '', $count );

	if ( 1 === $category->count ) {
		$html = sprintf( esc_html_x( '%s Article', 'articles count for 1 article', 'helppress' ), $count );
	} else {
		$html = sprintf( esc_html_x( '%s Articles', 'articles count for 0 or > 1 articles', 'helppress' ), $count );
	}

	echo $html;

}
endif;

/**
 * Returns Knowledge base archive URL.
 *
 * @since 1.0.0
 *
 * @return string Archive URL.
 */
function helppress_get_kb_url() {

	if ( helppress_get_option( 'show_on_front' ) ) {
		$url = home_url( '/' );
	} else {
		$url = get_post_type_archive_link( 'hp_article' );
	}

	return $url;

}

/**
 * Returns post format like `get_post_format()`, but returns `standard` if no format is set.
 *
 * @since 1.0.0
 *
 * @param integer $post_id Post ID to get format for.
 * @return string Post format.
 */
function helppress_get_post_format( $post_id = null ) {

	if ( ! $post_id ) {
		$post_id = get_the_id();
	}

	$format = get_post_format( $post_id );
	$format = $format ? $format : 'standard';

	return apply_filters( 'helppress_get_post_format', $format );

}

/**
 * Returns whether is KB article.
 *
 * @since 1.0.0
 *
 * @return boolean
 */
function helppress_is_kb_article() {

	return is_singular( 'hp_article' ) && ! is_admin();

}

/**
 * Returns whether KB archive.
 *
 * @since 1.0.0
 *
 * @return boolean
 */
function helppress_is_kb_archive() {

	return is_post_type_archive( 'hp_article' ) && ! is_search() && ! is_admin();

}

/**
 * Returns whether KB category.
 *
 * @since 1.0.0
 *
 * @return boolean
 */
function helppress_is_kb_category() {

	return is_tax( 'hp_category' ) && ! is_admin();

}

/**
 * Returns whether KB tag.
 *
 * @since 1.0.0
 *
 * @return boolean
 */
function helppress_is_kb_tag() {

	return is_tax( 'hp_tag' ) && ! is_admin();

}

/**
 * Returns whether KB search.
 *
 * @since 1.0.0
 *
 * @return boolean
 */
function helppress_is_kb_search() {

	return is_search() && get_query_var( 'hps' ) && ! is_admin();

}

/**
 * Returns whether KB page.
 *
 * @since 1.0.0
 *
 * @return boolean
 */
function helppress_is_kb_page() {

	return (bool) helppress_get_kb_context();

}

/**
 * Gets the context of the current HelpPress page.
 *
 * Will return one of the following:
 *
 * - `'archive'`: main KB archive/index
 * - `'article'`: KB article page
 * - `'category'`: KB category archive
 * - `'tag'`: KB tag archive
 * - `'search'`: KB search results
 * - `false`: not a KB page
 *
 * @since 1.2.0
 *
 * @return string|false Context string if KB page, false if not.
 */
function helppress_get_kb_context() {

	$context = false;

	if ( helppress_is_kb_article() ) {
		$context = 'article';
	} elseif ( helppress_is_kb_archive() ) {
		$context = 'archive';
	} elseif ( helppress_is_kb_category() ) {
		$context = 'category';
	} elseif ( helppress_is_kb_tag() ) {
		$context = 'tag';
	} elseif ( helppress_is_kb_search() ) {
		$context = 'search';
	}

	$context = apply_filters( 'helppress_get_kb_context', $context );

	return $context;

}

/**
 * The query variable to use in custom `WP_Query` objects to make pagination work.
 *
 * @since 1.0.2
 *
 * @return int `page` or `paged` number.
 */
function helppress_page_num() {

	global $paged;

	return get_query_var( 'paged' ) ? get_query_var( 'paged' ) : ( get_query_var( 'page' ) ? get_query_var( 'page' ) : 1 );

}
