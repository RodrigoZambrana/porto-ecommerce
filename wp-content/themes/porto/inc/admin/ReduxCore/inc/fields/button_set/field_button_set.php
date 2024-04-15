<?php

	/**
	 * Redux Framework is free software: you can redistribute it and/or modify
	 * it under the terms of the GNU General Public License as published by
	 * the Free Software Foundation, either version 2 of the License, or
	 * any later version.
	 * Redux Framework is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	 * GNU General Public License for more details.
	 * You should have received a copy of the GNU General Public License
	 * along with Redux Framework. If not, see <http://www.gnu.org/licenses/>.
	 *
	 * @package     Redux_Field
	 * @subpackage  Button_Set
	 * @author      Daniel J Griffiths (Ghost1227)
	 * @author      Dovy Paukstys
	 * @author      Kevin Provance (kprovance)
	 * @version     3.0.0
	 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Don't duplicate me!
if ( ! class_exists( 'ReduxFramework_button_set' ) ) {

	/**
	 * Main ReduxFramework_button_set class
	 *
	 * @since       1.0.0
	 */
	#[\AllowDynamicProperties]
	class ReduxFramework_button_set {

		/**
		 * Holds configuration settings for each field in a model.
		 * Defining the field options
		 *
		 * @param array $arr (See above)
		 *
		 * @return Object A new editor object.
		 * */
		static $_properties = array(
			'id' => 'Identifier',
		);

		/**
		 * Field Constructor.
		 * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
		 *
		 * @since       1.0.0
		 * @access      public
		 * @return      void
		 */
		function __construct( $field, $value, $parent ) {
			if ( empty( $field ) ) {
				$field = array();
			}
			if ( empty( $value ) ) {
				$value = '';
			}
			$this->parent = $parent;
			$this->field  = $field;
			$this->value  = $value;
		}

		/**
		 * Field Render Function.
		 * Takes the vars and outputs the HTML for the field in the settings
		 *
		 * @since       1.0.0
		 * @access      public
		 * @return      void
		 */
		public function render() {
			if ( ! empty( $this->field['data'] ) && empty( $this->field['options'] ) ) {
				if ( empty( $this->field['args'] ) ) {
					$this->field['args'] = array();
				}

				$this->field['options'] = $this->parent->get_wordpress_data( $this->field['data'], $this->field['args'], $this->value );

				if ( empty( $this->field['options'] ) ) {
					return;
				}
			}

			$is_multi = ( isset( $this->field['multi'] ) && true === $this->field['multi'] ) ? true : false;

			$name = $this->field['name'] . $this->field['name_suffix'];

			// multi => true renders the field multi-selectable (checkbox vs radio).
			echo '<div class="buttonset ui-buttonset">';

			if ( $is_multi ) {
				$s = '';

				if ( empty( $this->value ) ) {
					$s = $name;
				}

				echo '<input type="hidden" data-name="' . esc_attr( $name ) . '" class="buttonset-empty" name="' . esc_attr( $s ) . '" value=""/>';

				$name = $name . '[]';
			}

			foreach ( $this->field['options'] as $k => $v ) {
				$selected = '';

				if ( $is_multi ) {
					$post_value = '';
					$type       = 'checkbox';

					if ( ! empty( $this->value ) && ! is_array( $this->value ) ) {
						$this->value = array( $this->value );
					}

					if ( is_array( $this->value ) && in_array( (string) $k, $this->value, true ) ) {
						$selected   = 'checked="checked"';
						$post_value = $k;
					}
				} else {
					$type = 'radio';

					if ( is_scalar( $this->value ) ) {
						$selected = checked( $this->value, $k, false );
					}
				}

				$the_val     = $k;
				$the_name    = $name;
				$data_val    = '';
				$multi_class = '';

				if ( $is_multi ) {
					$the_val     = '';
					$the_name    = '';
					$data_val    = ' data-val=' . $k;
					$hidden_name = $name;
					$multi_class = 'multi ';

					if ( '' === $post_value ) {
						$hidden_name = '';
					}

					echo '<input type="hidden" class="buttonset-check" id="' . esc_attr( $this->field['id'] ) . '-buttonset' . esc_attr( $k ) . '-hidden" name="' . esc_attr( $hidden_name ) . '" value="' . esc_attr( $post_value ) . '"/>';
				}

				echo '<input' . esc_attr( $data_val ) . ' data-id="' . esc_attr( $this->field['id'] ) . '" type="' . esc_attr( $type ) . '" id="' . esc_attr( $this->field['id'] ) . '-buttonset' . esc_attr( $k ) . '" name="' . esc_attr( $the_name ) . '" class="buttonset-item ' . esc_attr( $multi_class ) . esc_attr( $this->field['class'] ) . '" value="' . esc_attr( $the_val ) . '" ' . esc_html( $selected ) . '/>';
				echo '<label for="' . esc_attr( $this->field['id'] ) . '-buttonset' . esc_attr( $k ) . '">';

				if ( is_array( $v ) && isset( $v['hint'] ) && ! '' == $v['hint'] ) {
					
					// In case docs are ignored.
					$titleParam   = isset( $v['hint']['title'] ) ? $v['hint']['title'] : '';
					$contentParam = isset( $v['hint']['content'] ) ? $v['hint']['content'] : '';

					// Set hint html with appropriate position css
					echo esc_html( $v['label'] );
					echo '<div class="redux-hint-qtip" style="float:right; font-size: 12px; color:lightgray;" qtip-title="' . $titleParam . '" qtip-content="' . $contentParam . '">&nbsp;<i class="Simple-Line-Icons-question"></i></div>';
					
				} else {
					echo esc_html( $v );
				}
				echo '</label>';
			}

			echo '</div>';
		}

		/**
		 * Enqueue Function.
		 * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
		 *
		 * @since       1.0.0
		 * @access      public
		 * @return      void
		 */
		public function enqueue() {

			if ( ! wp_script_is( 'redux-field-button-set-js' ) ) {
				wp_enqueue_script(
					'redux-field-button-set-js',
					ReduxFramework::$_url . 'inc/fields/button_set/field_button_set' . Redux_Functions::isMin() . '.js',
					array( 'jquery', 'jquery-ui-core', 'redux-js' ),
					time(),
					true
				);
			}
		}
	}
}