<?php

	/**
	 * Create meta boxes for editing pages in WordPress
	 * Compatible with custom post types in WordPress 3.0
	 *
	 * Support input types: text, textarea, checkbox, radio box, select, file, image
	 *
	 * @author: Rilwis
	 * @url: http://www.deluxeblogtips.com/2010/04/how-to-create-meta-box-wordpress-post.html
	 * @version: 2.2
	 *
	 * Changelog:
	 * - 2.2: add enctype to post form (fix upload bug), thanks to http://www.hashbangcode.com/blog/add-enctype-wordpress-post-and-page-forms-471.html
	 * - 2.1: add file upload, image upload support
	 * - 2.0: oop code, support multiple post types, multiple meta boxes
	 * - 1.0: procedural code
	 */
	$meta_boxes = array( );

	// Slide layout
	$meta_boxes[] = array(
		'id' => 'pows_slide_settings',
		'title' => __( 'Slide settings (power slider)', pows_slug() ),
		'pages' => array( 'post' ),
		'context' => 'normal',
		'priority' => 'high',
		'fields' => array(
			array(
				'name' => __( 'Slide layout', pows_slug() ),
				'desc' => __( 'Choose layout type', pows_slug() ),
				'id' => 'pows_layout',
				'type' => 'radio',
				'options' => array(
					array( 'name' => __( 'Image from left', pows_slug() ), 'value' => 'left' ),
					array( 'name' => __( 'Image from right', pows_slug() ), 'value' => 'right' ),
					array( 'name' => __( 'Fullsized image', pows_slug() ), 'value' => 'full' )
				)
			),
			array(
				'name' => __( 'Slide link', pows_slug() ),
				'desc' => __( 'Enter custom link for this slide', pows_slug() ),
				'id' => 'pows_link',
				'type' => 'text'
			),
		)
	);

	/* You should not edit the code below */

	foreach ( $meta_boxes as $meta_box ) {
		$my_box = new My_meta_box( $meta_box );
	}

	class My_meta_box {

		protected $_meta_box;

		// create meta box based on given data
		function __construct( $meta_box ) {
			if ( !is_admin() )
				return;

			$this->_meta_box = $meta_box;

			// fix upload bug: http://www.hashbangcode.com/blog/add-enctype-wordpress-post-and-page-forms-471.html
			$current_page = substr( strrchr( $_SERVER['PHP_SELF'], '/' ), 1, -4 );
			if ( $current_page == 'page' || $current_page == 'page-new' || $current_page == 'post' || $current_page == 'post-new' ) {
				add_action( 'admin_head', array( &$this, 'add_post_enctype' ) );
			}

			add_action( 'admin_menu', array( &$this, 'add' ) );

			add_action( 'save_post', array( &$this, 'save' ) );
		}

		function add_post_enctype() {
			echo '
		<script type="text/javascript">
		jQuery(document).ready(function(){
			jQuery("#post").attr("enctype", "multipart/form-data");
			jQuery("#post").attr("encoding", "multipart/form-data");
		});
		</script>';
		}

		/// Add meta box for multiple post types
		function add() {
			foreach ( $this->_meta_box['pages'] as $page ) {
				add_meta_box( $this->_meta_box['id'], $this->_meta_box['title'], array( &$this, 'show' ), $page, $this->_meta_box['context'], $this->_meta_box['priority'] );
			}
		}

		// Callback function to show fields in meta box
		function show() {
			global $post;

			// Use nonce for verification
			echo '<input type="hidden" name="mytheme_meta_box_nonce" value="', wp_create_nonce( basename( __FILE__ ) ), '" />';

			echo '<table class="form-table">';

			foreach ( $this->_meta_box['fields'] as $field ) {
				// get current post meta data
				$meta = get_post_meta( $post->ID, $field['id'], true );

				echo '<tr>',
				'<th style="width:20%"><label for="', $field['id'], '">', $field['name'], '</label></th>',
				'<td>';
				switch ( $field['type'] ) {
					case 'text':
						echo '<input type="text" name="', $field['id'], '" id="', $field['id'], '" value="', $meta ? $meta : $field['std'], '" style="width:80%" />',
						'<br />', $field['desc'];
						break;
					case 'textarea':
						echo '<textarea name="', $field['id'], '" id="', $field['id'], '" style="width:80%;height:120px">', $meta ? $meta : $field['std'], '</textarea>',
						'<br />', $field['desc'];
						break;
					case 'select':
						echo '<select name="', $field['id'], '" id="', $field['id'], '">';
						foreach ( $field['options'] as $option ) {
							echo '<option', $meta == $option ? ' selected="selected"' : '', '>', $option, '</option>';
						}
						echo '</select>';
						echo '<br/>' . $field['desc'];
						break;
					case 'radio':
						foreach ( $field['options'] as $option ) {
							echo '<label><input type="radio" name="', $field['id'], '" value="', $option['value'], '"', $meta == $option['value'] ? ' checked="checked"' : '', ' /> ', $option['name'] . '</label><br/>';
						}
						break;
					case 'checkbox':
						echo '<input type="checkbox" name="', $field['id'], '" id="', $field['id'], '"', $meta ? ' checked="checked"' : '', ' />';
						break;
					case 'file':
						echo $meta ? "$meta<br />" : '', '<input type="file" name="', $field['id'], '" id="', $field['id'], '" />',
						'<br />', $field['desc'];
						break;
					case 'image':
						echo $meta ? "<img src=\"$meta\" width=\"120\" height=\"150\" /><br />$meta<br />" : '', '<input type="file" name="', $field['id'], '" id="', $field['id'], '" />',
						'<br />', $field['desc'];
						break;
				}
				echo '<td>',
				'</tr>';
			}

			echo '</table>';
		}

		// Save data from meta box
		function save( $post_id ) {
			// verify nonce
			if ( !wp_verify_nonce( $_POST['mytheme_meta_box_nonce'], basename( __FILE__ ) ) ) {
				return $post_id;
			}

			// check autosave
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return $post_id;
			}

			// check permissions
			if ( 'page' == $_POST['post_type'] ) {
				if ( !current_user_can( 'edit_page', $post_id ) ) {
					return $post_id;
				}
			} elseif ( !current_user_can( 'edit_post', $post_id ) ) {
				return $post_id;
			}

			foreach ( $this->_meta_box['fields'] as $field ) {
				$name = $field['id'];

				$old = get_post_meta( $post_id, $name, true );
				$new = $_POST[$field['id']];

				if ( $field['type'] == 'file' || $field['type'] == 'image' ) {
					$file = wp_handle_upload( $_FILES[$name], array( 'test_form' => false ) );
					$new = $file['url'];
				}

				if ( $new && $new != $old ) {
					update_post_meta( $post_id, $name, $new );
				} elseif ( '' == $new && $old && $field['type'] != 'file' && $field['type'] != 'image' ) {
					delete_post_meta( $post_id, $name, $old );
				}
			}
		}

	}

?>
