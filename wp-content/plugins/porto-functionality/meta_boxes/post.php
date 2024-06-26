<?php

// Meta Fields
function porto_post_meta_fields() {

	// Slideshow Types
	$media_types = porto_ct_post_media_types();

	return apply_filters( 
		'porto_cpt_meta_fields',
		array(
			// Slideshow Type
			'slideshow_type' => array(
				'name'    => 'slideshow_type',
				'title'   => __( 'Media Type', 'porto-functionality' ),
				'type'    => 'radio',
				'default' => 'images',
				'options' => $media_types,
			),
			// Video & Audio Embed Code
			'video_code'     => array(
				'name'     => 'video_code',
				'title'    => __( 'Video & Audio Embed Code or Content', 'porto-functionality' ),
				'desc'     => __( 'Paste the iframe code of the Flash (YouTube or Vimeo etc) or Input the shortcodes. Only necessary when the post type is Video & Audio.', 'porto-functionality' ),
				'type'     => 'textarea',
				'required' => array(
					'name'  => 'slideshow_type',
					'value' => 'video',
				),
			),
			// External URL
			'external_url'   => array(
				'name'  => 'external_url',
				'title' => __( 'External URL', 'porto-functionality' ),
				'desc'  => __( 'Input website url if post format is link.', 'porto-functionality' ),
				'type'  => 'text',
			),
			// Quote Text
			/*'quote_text'   => array(
				'name'  => 'quote_text',
				'title' => __( 'Quote', 'porto-functionality' ),
				'desc'  => __( 'Input quote if post format is quote.', 'porto-functionality' ),
				'type'  => 'text',
			),*/
			// Layout
			'post_layout'    => array(
				'name'    => 'post_layout',
				'title'   => __( 'Post Layout', 'porto-functionality' ),
				'type'    => 'imageselect',
				'default' => 'default',
				'options' => array_merge(
					array(
						'default' => array(
							'title' => __( 'Default', 'porto-functionality' ),
							'img'   => PORTO_OPTIONS_URI . '/svg/theme-option.svg',
						),
					),
					porto_ct_post_single_layouts()
				),
				'desc'    => sprintf( __( 'You can change %1$sglobal%2$s value in theme option.', 'porto-functionality' ), '<a href="' . porto_get_theme_option_url( 'post-content-layout' ) . '" target="_blank">', '</a>' ),
			),
			// Share
			'post_share'     => array(
				'name'    => 'post_share',
				'title'   => __( 'Share', 'porto-functionality' ),
				'type'    => 'radio',
				'default' => '',
				'options' => porto_ct_share_options(),
				'desc'    => sprintf( __( 'You can change %1$sglobal%2$s value in theme option.', 'porto-functionality' ), '<a href="' . porto_get_theme_option_url( 'post-share' ) . '" target="_blank">', '</a>' ),
			),

			// Like Count
			'like_count'     => array(
				'name'    => 'like_count',
				'title'   => __( 'Like Count', 'porto-functionality' ),
				'type'    => 'text',
				'default' => __( '0', 'porto-functionality' ),
			),
		)
	);
}

function porto_post_view_meta_fields() {
	$meta_fields = porto_ct_default_view_meta_fields( 'post' );
	// Layout
	$meta_fields['layout']['default'] = 'right-sidebar';
	return $meta_fields;
}

function porto_post_skin_meta_fields() {
	$meta_fields = porto_ct_default_skin_meta_fields();
	return $meta_fields;
}

// Show Meta Boxes
add_action( 'add_meta_boxes', 'porto_add_post_meta_boxes' );
if ( ! function_exists( 'porto_add_post_meta_boxes' ) ) {
	/**
	 * @todo 2.3.0 Legacy Mode
	 */
	function porto_add_post_meta_boxes() {
		if ( ! function_exists( 'get_current_screen' ) ) {
			return;
		}
		global $porto_settings;
		$screen = get_current_screen();
		if ( function_exists( 'add_meta_box' ) && $screen && 'post' == $screen->base && 'post' == $screen->id ) {
			add_meta_box( 'post-meta-box', __( 'Porto Post Options', 'porto-functionality' ), 'porto_post_meta_box', 'post', 'normal', 'high' );
			add_meta_box( 'view-meta-box', __( 'Porto View Options', 'porto-functionality' ), 'porto_post_view_meta_box', 'post', 'normal', 'low' );
			if ( $porto_settings['show-content-type-skin'] ) {
				add_meta_box( 'skin-meta-box', __( 'Porto Skin Options', 'porto-functionality' ), 'porto_post_skin_meta_box', 'post', 'normal', 'low' );
			}
		}
	}
}

function porto_post_meta_box() {
	$meta_fields = porto_post_meta_fields();
	porto_show_meta_box( $meta_fields );
}

function porto_post_view_meta_box() {
	$meta_fields = porto_post_view_meta_fields();
	porto_show_meta_box( $meta_fields );
}

function porto_post_skin_meta_box() {
	$meta_fields = porto_post_skin_meta_fields();
	porto_show_meta_box( $meta_fields );
}

// Save Meta Values
add_action( 'save_post', 'porto_save_post_meta_values' );
function porto_save_post_meta_values( $post_id ) {
	if ( ! function_exists( 'get_current_screen' ) ) {
		return;
	}
	$screen = get_current_screen();
	if ( $screen && $screen && 'post' == $screen->base && 'post' == $screen->id ) {
		porto_save_meta_value( $post_id, porto_post_meta_fields() );
		porto_save_meta_value( $post_id, porto_post_view_meta_fields() );
		porto_save_meta_value( $post_id, porto_post_skin_meta_fields() );
	}
}

// Remove in default custom field meta box
add_filter( 'is_protected_meta', 'porto_post_protected_meta', 10, 3 );
function porto_post_protected_meta( $protected, $meta_key, $meta_type ) {
	if ( ! function_exists( 'get_current_screen' ) ) {
		return $protected;
	}
	$screen = get_current_screen();
	if ( ! $protected && $screen && 'post' == $screen->base && 'post' == $screen->id ) {
		if ( array_key_exists( $meta_key, porto_post_meta_fields() )
			|| array_key_exists( $meta_key, porto_post_view_meta_fields() )
			|| array_key_exists( $meta_key, porto_post_skin_meta_fields() ) ) {
			$protected = true;
		}
	}
	return $protected;
}

////////////////////////////////////////////////////////////////////////

// Taxonomy Meta Fields
/**
 * Please use Porto Soft Mode. In Soft Mode, we don't support
 * these taxonomy options.
 *
 * @deprecated 2.3.0
 */
function porto_category_meta_fields() {
	global $porto_settings;

	$meta_fields = porto_ct_default_view_meta_fields( 'post', 'tax' );

	// Post Options
	$meta_fields = array_insert_before(
		'loading_overlay',
		$meta_fields,
		'post_options',
		array(
			'name'  => 'post_options',
			'title' => __( 'Archive Options', 'porto-functionality' ),
			'desc' => sprintf( __( 'Change default theme options. You can change %1$sglobal%2$s value in theme option.', 'porto-functionality' ), '<a href="' . porto_get_theme_option_url( 'blog-infinite' ) . '" target="_blank">', '</a>' ),
			'type'  => 'checkbox',
		)
	);

	// Infinite Scroll
	$meta_fields = array_insert_after(
		'post_options',
		$meta_fields,
		'post_infinite',
		array(
			'name'     => 'post_infinite',
			'title'    => __( 'Infinite Scroll', 'porto-functionality' ),
			'desc' => sprintf( __( 'Disable infinite scroll. You can change %1$sglobal%2$s value in theme option.', 'porto-functionality' ), '<a href="' . porto_get_theme_option_url( 'blog-infinite' ) . '" target="_blank">', '</a>' ),
			'type'     => 'checkbox',
			'required' => array(
				'name'  => 'post_options',
				'value' => 'post_options',
			),
		)
	);

	// Layout
	$meta_fields = array_insert_after(
		'post_infinite',
		$meta_fields,
		'post_layout',
		array(
			'name'     => 'post_layout',
			'title'    => __( 'Post Layout', 'porto-functionality' ),
			'type'     => 'imageselect',
			'default'  => 'large',
			'options'  => porto_ct_post_archive_layouts(),
			'required' => array(
				'name'  => 'post_options',
				'value' => 'post_options',
			),
			'desc'     => sprintf( __( 'You can change %1$sglobal%2$s value in theme option.', 'porto-functionality' ), '<a href="' . porto_get_theme_option_url( 'post-layout' ) . '" target="_blank">', '</a>' ),
		)
	);
	// Grid Columns
	$meta_fields = array_insert_after(
		'post_layout',
		$meta_fields,
		'post_grid_columns',
		array(
			'name'     => 'post_grid_columns',
			'title'    => __( 'Grid Columns', 'porto-functionality' ),
			'desc'     => __( 'Select the post columns in <strong>Grid or Masonry</strong> layout.', 'porto-functionality' ),
			'type'     => 'radio',
			'default'  => '3',
			'options'  => array(
				'2' => __( '2 Columns', 'porto-functionality' ),
				'3' => __( '3 Columns', 'porto-functionality' ),
				'4' => __( '4 Columns', 'porto-functionality' ),
			),
			'required' => array(
				'name'  => 'post_options',
				'value' => 'post_options',
			),
		)
	);

	if ( isset( $porto_settings['show-category-skin'] ) && $porto_settings['show-category-skin'] ) {
		$meta_fields = array_merge( $meta_fields, porto_ct_default_skin_meta_fields( true ) );
	}

	return $meta_fields;
}

$taxonomy             = 'category';
$table_name           = $wpdb->prefix . $taxonomy . 'meta';
$variable_name        = $taxonomy . 'meta';
$wpdb->$variable_name = $table_name;

// Add Meta Fields when edit taxonomy
if ( ! function_exists( 'porto_edit_category_meta_fields' ) ) {
	add_action( 'category_edit_form_fields', 'porto_edit_category_meta_fields', 100, 2 );
	/**
	 * Please use Porto Soft Mode. In Soft Mode, we don't support
	 * these taxonomy options.
	 *
	 * @deprecated 2.3.0
	 */
	function porto_edit_category_meta_fields( $tag, $taxonomy ) {
		_deprecated_function( __METHOD__, '2.3.0', sprintf( '<b>%s</b> in %s', esc_html__( 'Porto Soft Mode', 'porto-functionality' ), esc_html__( 'Optimize Wizard', 'porto-functionality' ) ) );
		if ( 'category' !== $taxonomy ) {
			return;
		}
		porto_edit_tax_meta_fields( $tag, $taxonomy, porto_category_meta_fields() );
	}
}

// Save Meta Values
if ( ! function_exists( 'porto_save_category_meta_values' ) ) {
	add_action( 'edit_term', 'porto_save_category_meta_values', 100, 3 );
	/**
	 *
	 * Please use Porto Soft Mode. In Soft Mode, we don't support
	 * these taxonomy options.
	 *
	 * @deprecated 2.3.0
	 */
	function porto_save_category_meta_values( $term_id, $tt_id, $taxonomy ) {
		if ( 'category' !== $taxonomy ) {
			return;
		}
		porto_create_tax_meta_table( $taxonomy );
		return porto_save_tax_meta_values( $term_id, $taxonomy, porto_category_meta_fields() );
	}
}

// Delete Meta Values
if ( ! function_exists( 'porto_delete_category_meta_values' ) ) {
	add_action( 'delete_term', 'porto_delete_category_meta_values', 10, 5 );
	/**
	 * Please use Porto Soft Mode. In Soft Mode, we don't support
	 * these taxonomy options.
	 *
	 * @deprecated 2.3.0
	 */
	function porto_delete_category_meta_values( $term_id, $tt_id, $taxonomy, $deleted_term, $object_ids ) {
		if ( 'category' !== $taxonomy ) {
			return;
		}
		return porto_delete_tax_meta_values( $term_id, $taxonomy, porto_category_meta_fields() );
	}
}
