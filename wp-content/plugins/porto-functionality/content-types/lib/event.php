<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Porto_Event_Query Class.
 *
 * Contains the query functions which alter the front-end post queries and loops
 */
class Porto_Event_Query {

	/** @public array Query vars to add to wp */
	public $query_vars = array();

	/**
	 * Stores chosen attributes
	 * @var array
	 */
	private static $_chosen_attributes;

	/**
	 * Constructor for the query class. Hooks in methods.
	 *
	 * @access public
	 */
	public function __construct() {
		if ( ! is_admin() ) {
			add_action( 'pre_get_posts', array( $this, 'pre_get_posts' ) );
			add_action( 'wp', array( $this, 'remove_event_query' ) );
		}
	}

	/**
	 * Hook into pre_get_posts to do the main event query.
	 *
	 * @param mixed $q query object
	 */
	public function pre_get_posts( $q ) {
		// We only want to affect the main query
		if ( ! $q->is_main_query() ) {
			return;
		}

		// Fix for verbose page rules
		if ( $GLOBALS['wp_rewrite']->use_verbose_page_rules && isset( $q->queried_object->ID ) && porto_events_page_id() && $q->queried_object->ID === porto_events_page_id() ) {
			$q->set( 'post_type', 'event' );
			$q->set( 'page', '' );
			$q->set( 'pagename', '' );

			// Fix conditional Functions
			$q->is_archive           = true;
			$q->is_post_type_archive = true;
			$q->is_singular          = false;
			$q->is_page              = false;
		}

		// Fix for endpoints on the homepage
		if ( $q->is_home() && 'page' === get_option( 'show_on_front' ) && absint( get_option( 'page_on_front' ) ) !== absint( $q->get( 'page_id' ) ) ) {
			$_query = wp_parse_args( $q->query );
			if ( ! empty( $_query ) && array_intersect( array_keys( $_query ), array_keys( $this->query_vars ) ) ) {
				$q->is_page     = true;
				$q->is_home     = false;
				$q->is_singular = true;
				$q->set( 'page_id', (int) get_option( 'page_on_front' ) );
				add_filter( 'redirect_canonical', '__return_false' );
			}
		}

		// When orderby is set, WordPress shows posts. Get around that here.
		if ( $q->is_home() && 'page' === get_option( 'show_on_front' ) && porto_events_page_id() && absint( get_option( 'page_on_front' ) ) === porto_events_page_id() ) {
			$_query = wp_parse_args( $q->query );
			if ( empty( $_query ) || ! array_diff( array_keys( $_query ), array( 'preview', 'page', 'paged', 'cpage', 'orderby' ) ) ) {
				$q->is_page = true;
				$q->is_home = false;
				$q->set( 'page_id', (int) get_option( 'page_on_front' ) );
				$q->set( 'post_type', 'event' );
			}
		}

		// Fix event feeds
		if ( $q->is_feed() && $q->is_post_type_archive( 'event' ) ) {
			$q->is_comment_feed = false;
		}

		// Special check for events page with the event archive on front
		if ( $q->is_page() && 'page' === get_option( 'show_on_front' ) && porto_events_page_id() && absint( $q->get( 'page_id' ) ) === porto_events_page_id() ) {

			// This is a front-page events page
			$q->set( 'post_type', 'event' );
			$q->set( 'page_id', '' );

			if ( isset( $q->query['paged'] ) ) {
				$q->set( 'paged', $q->query['paged'] );
			}

			// Define a variable so we know this is the front page events later on
			define( 'EVENT_IS_ON_FRONT', true );

			// Get the actual WP page to avoid errors and let us use is_front_page()
			// This is hacky but works. Awaiting https://core.trac.wordpress.org/ticket/21096
			global $wp_post_types;

			$events_page = get_post( porto_events_page_id() );

			$wp_post_types['event']->ID         = $events_page->ID;
			$wp_post_types['event']->post_title = $events_page->post_title;
			$wp_post_types['event']->post_name  = $events_page->post_name;
			$wp_post_types['event']->post_type  = $events_page->post_type;
			$wp_post_types['event']->ancestors  = get_ancestors( $events_page->ID, $events_page->post_type );

			// Fix conditional Functions like is_front_page
			$q->is_singular          = false;
			$q->is_post_type_archive = true;
			$q->is_archive           = true;
			$q->is_page              = true;

			// Remove post type archive name from front page title tag
			add_filter( 'post_type_archive_title', '__return_empty_string', 5 );

			// Fix WP SEO
			if ( class_exists( 'WPSEO_Meta' ) ) {
				add_filter( 'wpseo_metadesc', array( $this, 'wpseo_metadesc' ) );
				add_filter( 'wpseo_metakey', array( $this, 'wpseo_metakey' ) );
			}

			// Only apply to event categories, the event post archive, the events page, event taxonomies
		} elseif ( ! $q->is_post_type_archive( 'event' ) && ! $q->is_tax( get_object_taxonomies( 'event' ) ) ) {
			return;
		}

		// And remove the pre_get_posts hook
		$this->remove_event_query();
	}

	/**
	 * WP SEO meta description.
	 *
	 * Hooked into wpseo_ hook already, so no need for function_exist.
	 *
	 * @access public
	 * @return string
	 */
	public function wpseo_metadesc() {
		return WPSEO_Meta::get_value( 'metadesc', porto_events_page_id() );
	}

	/**
	 * WP SEO meta key.
	 *
	 * Hooked into wpseo_ hook already, so no need for function_exist.
	 *
	 * @access public
	 * @return string
	 */
	public function wpseo_metakey() {
		return WPSEO_Meta::get_value( 'metakey', porto_events_page_id() );
	}

	/**
	 * Remove the query.
	 */
	public function remove_event_query() {
		remove_action( 'pre_get_posts', array( $this, 'pre_get_posts' ) );
	}
}

// Global for backwards compatibility.
$GLOBALS['porto_event_query'] = new Porto_Event_Query();

/**
 * Event Page Functions
 *
 * Functions related to pages and menus.
 */

function is_porto_events_page() {
	$events_page = porto_events_page_id();
	return ( is_post_type_archive( 'event' ) || ( $events_page && is_page( $events_page ) ) );
}

function porto_events_page_id() {
	global $porto_settings;

	$events_page = (int) ( ( isset( $porto_settings ) && isset( $porto_settings['event-archive-page'] ) && $porto_settings['event-archive-page'] ) ? $porto_settings['event-archive-page'] : 0 );
	return $events_page;
}

function is_porto_event() {
	return ( is_porto_events_page() || is_tax( get_object_taxonomies( 'event' ) ) || is_singular( array( 'event' ) ) ) ? true : false;
}

// Fix active class in nav for events page
function porto_event_nav_menu_item_classes( $menu_items ) {
	global $porto_settings;

	$enable_content_type = ( isset( $porto_settings ) && isset( $porto_settings['enable-event'] ) ) ? $porto_settings['enable-event'] : true;
	if ( ! $enable_content_type ) {
		return $menu_items;
	}

	$events_page    = porto_events_page_id();
	$page_for_posts = (int) get_option( 'page_for_posts' );

	foreach ( (array) $menu_items as $key => $menu_item ) {

		$classes = (array) $menu_item->classes;

		// Unset active class for blog page
		if ( $page_for_posts == $menu_item->object_id ) {
			$menu_items[ $key ]->current = false;

			if ( in_array( 'current_page_parent', $classes ) ) {
				unset( $classes[ array_search( 'current_page_parent', $classes ) ] );
			}

			if ( in_array( 'current-menu-item', $classes ) ) {
				unset( $classes[ array_search( 'current-menu-item', $classes ) ] );
			}

			// Set active state if this is the events page link
		} elseif ( is_porto_events_page() && $events_page == $menu_item->object_id && 'page' === $menu_item->object ) {
			$menu_items[ $key ]->current = true;
			$classes[]                   = 'current-menu-item';
			$classes[]                   = 'current_page_item';

			// Set parent state if this is a events page
		} elseif ( is_singular( 'event' ) && $events_page == $menu_item->object_id ) {
			$classes[] = 'current_page_parent';
		}

		$menu_items[ $key ]->classes = array_unique( $classes );
	}

	return $menu_items;
}
add_filter( 'wp_nav_menu_objects', 'porto_event_nav_menu_item_classes', 2 );

// Fix active class in wp_list_pages for events page.
function porto_event_list_pages( $pages ) {
	if ( is_porto_event() ) {
		$pages         = str_replace( 'current_page_parent', '', $pages );
		$event_page_id = 'page-item-' . porto_events_page_id();

		if ( is_porto_events_page() ) {
			$pages = str_replace( $event_page_id, $event_page_id . ' current_page_item', $pages );
		} else {
			$pages = str_replace( $event_page_id, $event_page_id . ' current_page_parent', $pages );
		}
	}

	return $pages;
}
add_filter( 'wp_list_pages', 'porto_event_list_pages' );
