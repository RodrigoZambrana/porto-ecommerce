<?php
$output = $label = $link = $icon = $skin = $link_color = $link_bg_color = $link_acolor = $link_abg_color = $el_class = '';
extract(
	shortcode_atts(
		array(
			'_id'             => '',
			'label'           => '',
			'link'            => '',
			'show_icon'       => false,
			'icon_type'       => 'fontawesome',
			'icon'            => '',
			'icon_image'      => '',
			'icon_simpleline' => '',
			'skin'            => 'custom',
			'link_color'      => '',
			'link_bg_color'   => '',
			'link_acolor'     => '',
			'link_abg_color'  => '',
			'el_class'        => '',
		),
		$atts
	)
);

$el_class = porto_shortcode_extract_class( $el_class );
if ( ! empty( $shortcode_class ) ) {
	$el_class .= $shortcode_class;
}

switch ( $icon_type ) {
	case 'simpleline':
		$icon_class = $icon_simpleline;
		break;
	case 'image':
		$icon_class = 'icon-image';
		break;
	default:
		$icon_class = $icon;
}

if ( ! $show_icon ) {
	$icon_class = '';
}

if ( $label ) {

	if ( ! empty( $_id ) ) {
		$el_class = trim( $el_class . ' ' . 'elementor-repeater-item-' . $_id );
	}

	$output = '<li class="' . esc_attr( $el_class ) . '">';

	if ( $link ) {
		$output .= '<a href="' . esc_url( $link ) . '">';
	} else {
		$output .= '<span>';
	}

	if ( $icon_class ) {
		$output .= '<i class="' . esc_attr( $icon_class ) . '">';
		if ( 'icon-image' == $icon_class && $icon_image ) {
			$icon_image = preg_replace( '/[^\d]/', '', $icon_image );
			$image_url  = wp_get_attachment_url( $icon_image );
			$image_url  = str_replace( array( 'http:', 'https:' ), '', $image_url );
			if ( $image_url ) {
				$alt_text = get_post_meta( $icon_image, '_wp_attachment_image_alt', true );
				$output  .= '<img alt="' . esc_attr( $alt_text ) . '" src="' . esc_url( $image_url ) . '">';
			}
		}
		$output .= '</i>';
	}

	$output .= $label;

	if ( $link ) {
		$output .= '</a>';
	} else {
		$output .= '</span>';
	}

	$output .= '</li>';
}

echo porto_filter_output( $output );
